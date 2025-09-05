# Client Manager Demo (Laravel 12 + Vue 3 + Inertia)

A tiny starter app that demonstrates a **client list manager** built with:

* **Laravel 12** (RESTful controllers, Form Requests, events/listeners, mail)
* **Vue 3 + Inertia.js** (SPA-like UX with server-side auth/validation)
* **Vite** (bundling) and **Wayfinder** (typed routes for the front-end)
* POPIA-minded data handling (consent logs, encryption for SA ID numbers)

---

## Features

* CRUD for Clients:

  * Name, Surname, Email, Mobile, Birth Date
  * **Language** (single select)
  * **Interests** (multi-select; pivot table)
  * **Identity** (type + encrypted ID number)
* Admin pages:

  * `/clients` – list & manage clients
  * `/admin/user/create` – create client form (Inertia page)
* Validation with `FormRequest` classes (Laravel 12 `ValidationRule` contract)
* POPIA helpers:

  * `consent_logs` with consent snapshots and IP
  * ID numbers encrypted with Laravel’s `Crypt`
* Event-driven email:

  * `RegisteredClient` event → `SendWelcomeEmail` listener → `WelcomeClientMail` mailable

---

## Requirements

* PHP ≥ 8.2
* Node.js ≥ 18
* MySQL ≥ 8
* Composer
* (Optional) Queue worker for async email delivery

---

## Install & Run

```bash
# 1) Clone and install dependencies
composer install
npm install

# 2) Copy environment and set your details
cp .env.example .env
php artisan key:generate
# set DB_*, MAIL_* variables in .env

# 3) Migrate and (optionally) seed reference data
php artisan migrate
php artisan db:seed --class=LanguagesTableSeeder
php artisan db:seed --class=IdentityTypesTableSeeder
php artisan db:seed --class=InterestsTableSeeder

# 4) Build frontend assets
npm run dev    # during development (HMR)
# or
npm run build  # for production

# 5) Serve the app
php artisan serve

# 6) (Optional) Run a queue worker (for queued emails)
php artisan queue:work
```

---

## Database Model

**Core tables**

* `clients`
  `id, name, surname, email, mobile, birth_date, language_id, timestamps`

* `languages`
  `id, name`

* `interests`
  `id, name`

* `client_interest` (pivot)
  `id, client_id, interest_id` (FKs cascade on delete)

**Identity**

* `identity_types`
  `id, name` (e.g., South African ID, Passport)

* `identities`
  `id, client_id, identity_type_id, value_encrypted, timestamps`

  > `value_encrypted` holds **encrypted** ID numbers via `Crypt::encryptString()`.

**POPIA**

* `consent_logs`
  `id, user_id|client_id, consent_type, consent_text_snapshot, ip_address, created_at`

> Ensure `APP_KEY` is set in `.env` so encrypted fields can be decrypted later.

---

## Routes (Server)

`routes/web.php` (high-level)

```php
use App\Http\Controllers\Admin\AdminController;
use App\Models\{Client, Language, Interest, IdentityType};
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    // List
    Route::get('clients', function () {
        return Inertia::render('clients/ManageClients', [
            'clients' => Client::with(['language:id,name','interests:id,name'])
                ->get(['id','name','surname','email','mobile','birth_date','language_id'])
                ->map(fn ($c) => [
                    'id'        => $c->id,
                    'name'      => $c->name,
                    'surname'   => $c->surname,
                    'email'     => $c->email,
                    'mobile'    => $c->mobile,
                    'birth_date'=> optional($c->birth_date)?->toDateString(),
                    'language'  => $c->language?->only('id','name'),
                    'interests' => $c->interests->map->only('id','name')->all(),
                ]),
        ]);
    })->name('clients.index');

    // Create (Inertia form page)
    Route::get('/admin/user/create', function () {
        return Inertia::render('forms/CreateUser', [
            'languages'     => Language::select('id','name')->get(),
            'interests'     => Interest::select('id','name')->get(),
            'identityTypes' => IdentityType::select('id','name')->get(),
            'postUrl'       => route('clients.store'),
        ]);
    })->name('clients.create.form');

    // Store / Update / Delete
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('clients.store');
    Route::put('/admin/users/{client}', [AdminController::class, 'updateUser'])->name('clients.update');
    Route::delete('/admin/users/{client}', [AdminController::class, 'destroyUser'])->name('clients.destroy');
});
```

> **Wayfinder note:** If you use `@laravel/vite-plugin-wayfinder`, ensure named routes exist and `php artisan wayfinder:generate --with-form` runs without errors. Missing names or duplicate names will break the TS route index.

---

## Frontend Pages (Vue + Inertia)

* `resources/js/pages/clients/ManageClients.vue`
  Lists clients with **Edit** and **Delete** actions, and a **Create Client** button linking to the create form.

* `resources/js/pages/forms/CreateUser.vue`
  Client create form:

  * Inertia `useForm()`
  * Validates with backend `StoreNewUserPostRequest`
  * On success: redirects back to `/clients` (via controller)

> **CSRF**: Ensure your root Blade layout includes
> `<meta name="csrf-token" content="{{ csrf_token() }}">`.
> The form attaches `_token` before posting.

---

## Validation

* `app/Http/Requests/StoreNewUserPostRequest.php`
* `app/Http/Requests/UpdateUserPostRequest.php`

**Custom Rule**
`app/Rules/SouthAfricanId.php` implements `Illuminate\Contracts\Validation\ValidationRule` (Laravel 12) and performs a Luhn checksum on 13-digit IDs.

---

## Controllers

* `App\Http\Controllers\Admin\AdminController`

  * `storeUser(StoreNewUserPostRequest $request)`
    Creates `Client`, optional `Identity`, `Interests` sync, optional `ConsentLog`.
    Returns a **303 redirect** to `clients.index` for a proper Inertia flow and dispatches a `RegisteredClient` event.

  * `updateUser(UpdateUserPostRequest $request, Client $client)`
    Updates fields, creates/updates/deletes identity depending on inputs, syncs interests. Returns a **303 redirect** to `clients.index`.

  * `destroyUser(Client $client)`
    Deletes the client (and cascades where defined). Returns **303 redirect** to `clients.index`.

---

## Events, Listeners & Mail

* **Event:** `App\Events\RegisteredClient` (`$afterCommit = true`)
* **Listener:** `App\Listeners\SendWelcomeEmail` (implements `ShouldQueue`)
* **Mailable:** `App\Mail\WelcomeClientMail` (Laravel 12 `Envelope`/`Content` stubs)
* **View:** `resources/views/emails/welcome-client.blade.php`

Mail config in `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="Client Manager"
```

Run a worker if queued:

```bash
php artisan queue:work
```

---

## POPIA Hints & Auditability

* Store **consent snapshots** in `consent_logs` with IP and timestamps.
* Use inline purpose text for fields on the front-end.
* Encrypt ID numbers using `Crypt`. Keep `APP_KEY` safe.
* Implement data export/deletion endpoints if required.
* Add retention jobs if needed (e.g., anonymize after X years).

---

## Troubleshooting

* **Inertia “plain JSON response” error**
  Ensure controller actions that service Inertia forms **return an Inertia redirect** (e.g., `return to_route('clients.index')->setStatusCode(303);`) not raw JSON.

* **Wayfinder build failures** (e.g., “route X is not exported”)
  Make sure the named route exists in `routes/web.php`. Then:

  ```bash
  php artisan route:clear
  php artisan optimize:clear
  php artisan wayfinder:generate --with-form
  npm run build
  ```

* **Emails not sending**
  Check `.env` mail settings. If listener is `ShouldQueue`, remember to run `php artisan queue:work`.

* **CSRF issues**
  Include `<meta name="csrf-token">` in `resources/views/app.blade.php` and attach `_token` (or use Sanctum’s `/sanctum/csrf-cookie`).

---

## Scripts

```bash
# development
npm run dev
php artisan serve

# production build
npm run build

# tests (if you add them)
php artisan test
```

---

## Project Structure (high-level)

```
app/
  Events/RegisteredClient.php
  Http/Controllers/Admin/AdminController.php
  Http/Requests/{StoreNewUserPostRequest,UpdateUserPostRequest}.php
  Listeners/SendWelcomeEmail.php
  Mail/WelcomeClientMail.php
  Models/{Client,Language,Interest,Identity,IdentityType,ConsentLog}.php

database/
  migrations/...
  seeders/{LanguagesTableSeeder,IdentityTypesTableSeeder,InterestsTableSeeder}.php

resources/
  js/
    pages/
      clients/ManageClients.vue
      forms/CreateUser.vue
    components/ui/{button,input,label}.tsx|vue (depends on your kit)
  views/
    app.blade.php
    emails/welcome-client.blade.php
```

---

## License

MIT (or your preferred license).
