<?php

namespace app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewUserPostRequest;
use App\Http\Requests\UpdateUserPostRequest;
use App\Models\ConsentLog;
use App\Models\Identity;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Events\RegisteredClient;

class AdminController extends Controller
{
    /**
     * Create a new user + related data
     * Accepts application/x-www-form-urlencoded
     *
     * @var StoreNewUserPostRequest
     */
    public function storeUser(StoreNewUserPostRequest $request)
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data, $request) {
            // User
            $client = new Client();
            $client->name = $data['name'];
            $client->surname = $data['surname'];
            $client->email = $data['email'];
            $client->mobile = $data['mobile'];
            $client->birth_date = $data['birth_date'] ?? null;
            $client->language_id = $data['language_id'] ?? null;
            $client->save();

            // Dispatch AFTER COMMIT so no emails go out on rollback
            DB::afterCommit(function () use ($client) {
                event(new RegisteredClient($client));
            });

            // Identity (optional)
            if (! empty($data['id_number']) && ! empty($data['identity_type_id'])) {
                $identity = new Identity();
                $identity->client_id = $client->id;
                $identity->identity_type_id = $data['identity_type_id'];
                $identity->value_encrypted = Crypt::encryptString($data['id_number']);
                $identity->save();
            }

            // Interests (optional)
            if (array_key_exists('interests', $data)) {
                $client->interests()->sync($data['interests'] ?? []);
            }

            // Consent logs (optional, still handled outside validation rules)
            if ($request->has('consent_marketing')) {
                ConsentLog::create([
                    'user_id' => $client->id,
                    'consent_type' => 'marketing',
                    'consent_text_snapshot' => $request->input('consent_text_marketing', 'Marketing consent.'),
                    'ip_address' => $request->ip(),
                ]);
            }

            if ($request->has('consent_id_storage')) {
                ConsentLog::create([
                    'user_id' => $client->id,
                    'consent_type' => 'id_storage',
                    'consent_text_snapshot' => $request->input('consent_text_id', 'ID storage consent.'),
                    'ip_address' => $request->ip(),
                ]);
            }
        });

        return redirect()
        ->route('clients.index')
        ->setStatusCode(303)
        ->with('success', 'Client created successfully.');
    }

    /**
     * Update a client + related data
     */
    public function updateUser(UpdateUserPostRequest $request, Client $client)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $client) {
            // Basic fields
            foreach (['name','surname','email','mobile','birth_date','language_id'] as $f) {
                if (array_key_exists($f, $data)) {
                    $client->{$f} = $data[$f];
                }
            }
            $client->save();

            // Identity (create/update/delete)
            if (array_key_exists('id_number', $data) || array_key_exists('identity_type_id', $data)) {
                $identity = $client->identity()->first();

                $hasValues = !empty($data['identity_type_id']) || !empty($data['id_number']);

                if ($hasValues) {
                    if (!$identity) {
                        $identity = new Identity();
                        $identity->client_id = $client->id;
                    }
                    if (array_key_exists('identity_type_id', $data)) {
                        $identity->identity_type_id = $data['identity_type_id'];
                    }
                    if (array_key_exists('id_number', $data) && $data['id_number'] !== null && $data['id_number'] !== '') {
                        $identity->value_encrypted = Crypt::encryptString($data['id_number']);
                    }
                    $identity->save();
                } elseif ($identity) {
                    $identity->delete();
                }
            }

            // Interests (sync when provided)
            if (array_key_exists('interests', $data)) {
                $client->interests()->sync($data['interests'] ?? []);
            }
        });

        // Inertia expects a redirect (303 for POST/PUT/PATCH/DELETE)
        return to_route('clients.index')->with('success', 'Client updated.')->setStatusCode(303);
    }

    /**
     * Delete a client (and related)
     */
    public function destroyUser(Client $client)
    {
        DB::transaction(function () use ($client) {
            // In case FKs aren’t cascading for identity:
            $client->identity()->delete();     // safe if none exists
            $client->interests()->detach();    // pivot cleanup
            $client->delete();
        });

        return to_route('clients.index')->with('success', 'Client deleted.')->setStatusCode(303);
    }
}
