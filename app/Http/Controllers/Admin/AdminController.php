<?php
namespace app\Http\Controllers\Admin;

use App\Http\Requests\StoreNewUserPostRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Identity;
use Illuminate\Support\Facades\Crypt;
use App\Models\ConsentLog;
use App\Http\Requests\UpdateUserPostRequest;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{

    /**
     * Create a new user + related data
     * Accepts application/x-www-form-urlencoded
     *
     * @var StoreNewUserPostRequest $request
     */
    public function storeUser(StoreNewUserPostRequest $request)
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data, $request) {
            // User
            $user = new User();
            $user->name = $data['name'];
            $user->surname = $data['surname'];
            $user->email = $data['email'];
            $user->mobile = $data['mobile'];
            $user->birth_date = $data['birth_date'] ?? null;
            $user->language_id = $data['language_id'] ?? null;
            $user->save();

            // Identity (optional)
            if (! empty($data['id_number']) && ! empty($data['identity_type_id'])) {
                $identity = new Identity();
                $identity->user_id = $user->id;
                $identity->identity_type_id = $data['identity_type_id'];
                $identity->id_number_enc = Crypt::encryptString($data['id_number']);
                $identity->save();
            }

            // Interests (optional)
            if (array_key_exists('interests', $data)) {
                $user->interests()->sync($data['interests'] ?? []);
            }

            // Consent logs (optional, still handled outside validation rules)
            if ($request->has('consent_marketing')) {
                ConsentLog::create([
                    'user_id' => $user->id,
                    'consent_type' => 'marketing',
                    'consent_text_snapshot' => $request->input('consent_text_marketing', 'Marketing consent.'),
                    'ip_address' => $request->ip()
                ]);
            }

            if ($request->has('consent_id_storage')) {
                ConsentLog::create([
                    'user_id' => $user->id,
                    'consent_type' => 'id_storage',
                    'consent_text_snapshot' => $request->input('consent_text_id', 'ID storage consent.'),
                    'ip_address' => $request->ip()
                ]);
            }

            return $user->fresh([
                'language',
                'interests',
                'identity'
            ]);
        });

        return response()->json([
            'status' => 'ok',
            'data' => $result
        ], 201);
    }

    /**
     * 
     * @param UpdateUserPostRequest $request
     * @param User $user
     * @return mixed
     */
    public function updateUser(UpdateUserPostRequest $request, User $user)
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data, $user) {
            // Basic fields
            foreach ([
                'name',
                'surname',
                'email',
                'mobile',
                'birth_date',
                'language_id'
            ] as $f) {
                if (array_key_exists($f, $data)) {
                    $user->{$f} = $data[$f];
                }
            }
            $user->save();

            // Identity (create/update/delete depending on inputs)
            if (array_key_exists('id_number', $data) || array_key_exists('identity_type_id', $data)) {
                $idModel = $user->identity()->firstOrNew([]);
                $idModel->identity_type_id = $data['identity_type_id'] ?? $idModel->identity_type_id;
                if (array_key_exists('id_number', $data)) {
                    $idModel->id_number_enc = $data['id_number'] ? Crypt::encryptString($data['id_number']) : null;
                }
                // If both are null -> delete identity; else save
                if (empty($idModel->identity_type_id) && empty($idModel->id_number_enc)) {
                    $user->identity()->delete();
                } else {
                    $idModel->user_id = $user->id;
                    $idModel->save();
                }
            }

            // Interests (sync only if provided)
            if (array_key_exists('interests', $data)) {
                $user->interests()->sync($data['interests'] ?? []);
            }

            return $user->fresh([
                'language',
                'interests',
                'identity'
            ]);
        });

        return response()->json([
            'status' => 'ok',
            'data' => $result
        ]);
    }
}
