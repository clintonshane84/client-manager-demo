<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Language;
use App\Models\Interest;
use App\Models\IdentityType;
use app\Http\Controllers\Admin\AdminController;
use App\Models\Client;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('clients', function () {
        return Inertia::render('clients/ManageClients', [
            'clients' => Client::with(['language:id,name', 'interests:id,name'])
            ->get(['id', 'name', 'surname', 'email', 'mobile', 'birth_date', 'language_id'])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'surname' => $u->surname,
                'email' => $u->email,
                'mobile' => $u->mobile,
                'birth_date' => optional($u->birth_date)?->toDateString(),
                'language' => $u->language ? ['id' => $u->language->id, 'name' => $u->language->name] : null,
                'interests' => $u->interests->map(fn ($i) => ['id' => $i->id, 'name' => $i->name])->all(),
            ]),
        ]);
    })->name('clients.index');


    Route::get('/admin/user/create', function (Client $client) {
        return Inertia::render('forms/CreateUser', [
            'languages' => Language::get(),
            'interests' => Interest::get(),
            'identityTypes' => IdentityType::get(),
            'postUrl' => route('clients.create', $client)
        ]);
    });

    Route::get('/clients/{client}/edit', function (Client $client) {
        $client->load(['language:id,name', 'interests:id,name', 'identity:client_id,identity_type_id']);

        return Inertia::render('forms/UpdateClient', [
            'client'        => [
                'id'         => $client->id,
                'name'       => $client->name,
                'surname'    => $client->surname,
                'email'      => $client->email,
                'mobile'     => $client->mobile,
                'birth_date' => optional($client->birth_date)?->toDateString(),
                'language'   => $client->language ? ['id' => $client->language->id, 'name' => $client->language->name] : null,
                'language_id' => $client->language_id,
                'interests'  => $client->interests->map(fn ($i) => ['id' => $i->id, 'name' => $i->name])->all(),
                'identity'   => $client->identity ? ['identity_type_id' => $client->identity->identity_type_id] : null,
            ],
            'languages'    => Language::select('id', 'name')->get(),
            'interests'    => Interest::select('id', 'name')->get(),
            'identityTypes' => IdentityType::select('id', 'name')->get(),
            'putUrl'       => route('clients.update', $client),
        ]);
    })->name('clients.edit');

    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('clients.create');

    // Update (choose one verb; PUT is common for full updates)
    Route::put('/admin/users/{client}', [AdminController::class, 'updateUser'])
    ->name('clients.update');

    // Delete
    Route::delete('/admin/users/{client}', [AdminController::class, 'destroyUser'])
    ->name('clients.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
