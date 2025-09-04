<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('clients', function () {
    return Inertia::render('clients/Index', [
        'users' => User::with(['language:id,name', 'interests:id,name'])
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
})->middleware(['auth', 'verified']);

Route::get('createUser', function () {
    return Inertia::render('forms/CreateUser');
})->middleware('auth');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
