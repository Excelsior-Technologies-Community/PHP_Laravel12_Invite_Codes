# PHP_Laravel12_Invite_Codes

## Introduction

PHP_Laravel12_Invite_Codes is a Laravel 12-based web application that implements a secure invite-based registration system using the mateusjunges/laravel-invite-codes.

This system ensures that only users with a valid invite code are allowed to register, providing controlled access to the application. It is ideal for:

- Private platforms
- SaaS applications
- Beta testing systems
- Restricted or limited-access applications

The project follows Laravel’s MVC architecture and includes modern UI design using Blade and Tailwind CSS.

---

## Project Overview

This project demonstrates a complete invite-based authentication system with the following features:

1) Invite-only registration

- Users must enter a valid invite code to register.

2) Invite Code Generation

- Authenticated users can generate unique invite codes.

3) Usage Tracking

- Each invite code tracks:
- Total allowed uses (max_usages)
- Current usage count (uses)

4) Validation & Restrictions

- Invalid invite codes are rejected
- Expired codes are blocked
- Overused codes are restricted

5) Modern UI

- Built using Blade templates with Tailwind CSS for a clean and responsive design.

6) Laravel 12 Architecture

- Follows best practices including:
- MVC structure
- Authentication (Breeze)
- Controller-based logic
- Database-driven system

---

##  Requirements

* PHP >= 8.2
* Composer
* Node.js & NPM
* MySQL
* Laravel 12

---

## Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Invite_Codes "12.*"
cd PHP_Laravel12_Invite_Codes
```

---

## Step 2: Setup Environment

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invite_codes_db
DB_USERNAME=root
DB_PASSWORD=
```

Run Migration Command:

```bash
php artisan migrate
```

---

## Step 3: Install Invite Codes Package

```bash
composer require mateusjunges/laravel-invite-codes
```

---

## Step 4: Publish Package Files

```bash
php artisan vendor:publish --provider="Junges\InviteCodes\InviteCodesServiceProvider"
```

---

## Step 5: Run Migrations

```bash
php artisan migrate
```

This creates:

* invites table

---

## Step 6: Setup Authentication (Laravel Breeze)

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run dev
php artisan migrate
```

---

## Step 7: Add Invite Code Field in Registration

Open:

```
resources/views/auth/register.blade.php
```

Add:

```html
        <!-- Invite Code (NEW FIELD) -->
        <div class="mt-4">
            <x-input-label for="invite_code" :value="__('Invite Code')" />
            <x-text-input id="invite_code" class="block mt-1 w-full" type="text" name="invite_code" :value="old('invite_code')" required />
            <x-input-error :messages="$errors->get('invite_code')" class="mt-2" />
        </div>
```

---

## Step 8: Validate Invite Code 

Open:

```
app/Http/Controllers/Auth/RegisteredUserController.php
```

Update:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function store(Request $request): RedirectResponse
    {
        //  Validate input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required'],
        ]);

        //  Find invite code
        $invite = DB::table('invites')
            ->where('code', $request->invite_code)
            ->first();

        //  Invalid code
        if (!$invite) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invalid invite code.',
            ]);
        }

        //  Expired check
        if ($invite->expires_at && now()->gt($invite->expires_at)) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invite code expired.',
            ]);
        }

        //  Usage limit check
        if ($invite->max_usages && $invite->uses >= $invite->max_usages) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invite code already used.',
            ]);
        }

        //  Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //  Increment invite usage
        DB::table('invites')
            ->where('code', $request->invite_code)
            ->increment('uses');

        //  Trigger event
        event(new Registered($user));

        //  Login user
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
```

---

## Step 9: Create Invite Controller

```bash
php artisan make:controller InviteController
```

Open:

```
app/Http/Controllers/InviteController.php
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InviteController extends Controller
{
    public function index()
    {
        $invites = DB::table('invites')->get();

        return view('invites.index', compact('invites'));
    }

    public function create()
    {
        DB::table('invites')->insert([
            'code' => strtoupper(uniqid()),
            'max_usages' => 1,
            'uses' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Invite code created successfully');
    }
}
```

---

## Step 10: Routes

Open:

```
routes/web.php
```

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InviteController; //  ADD THIS
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    //  Profile routes (default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //  Invite system routes (NEW)
    Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('/invites/create', [InviteController::class, 'create'])->name('invites.create');
});

require __DIR__.'/auth.php';
```

---

## Step 11: Create Blade View

Create:

```
resources/views/invites/index.blade.php
```

```html
<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                Invite Codes
            </h2>

            <!-- Generate Button -->
            <form action="{{ route('invites.create') }}" method="POST">
                @csrf
                <button class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow transition">
                    + Generate Invite Code
                </button>
            </form>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Table Card -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    
                    <!-- Table Head -->
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4">Max Uses</th>
                            <th class="px-6 py-4">Used</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-gray-100">
                        @forelse($invites as $invite)
                        <tr class="hover:bg-gray-50 transition">

                            <!-- Code -->
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $invite->code }}
                            </td>

                            <!-- Max Uses -->
                            <td class="px-6 py-4">
                                {{ $invite->max_usages ?? '∞' }}
                            </td>

                            <!-- Used -->
                            <td class="px-6 py-4">
                                {{ $invite->uses }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($invite->max_usages && $invite->uses >= $invite->max_usages)
                                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                        Expired
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                        Active
                                    </span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                                No invite codes found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## Step 12: Create First Invite Code Using Tinker

Run:

```bash
php artisan tinker
```
Then:

```
DB::table('invites')->insert([
    'code' => 'ABC1234',
    'max_usages' => 5,
    'uses' => 0,
    'created_at' => now(),
    'updated_at' => now(),
]);
```
This step is required only for the first time, because your database is empty and you need at least one invite code to start using the system.

---

## Step 13: Run Project

### 1. Start Laravel Backend

Open Terminal 1 and run:

```bash
php artisan serve
```
This will start your Laravel application

Visit:

```bash
http://127.0.0.1:8000
```

### 2. Start Frontend (Vite)

Open Terminal 2 and run:

```bash
npm run dev
```
This will:

- Compile Tailwind CSS
- Compile JavaScript
- Watch for changes automatically

---

## Output

<img src="screenshots/Screenshot 2026-03-24 112156.png" width="1000">

---

## Project Structure

```
PHP_Laravel12_Invite_Codes
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── RegisteredUserController.php
│   │   │   └── InviteController.php
│
├── bootstrap/
│
├── config/
│   ├── app.php
│   ├── database.php
│   └── invite-codes.php    
│
├── database/
│   ├── migrations/
│   │   └── xxxx_create_invites_table.php
│   
│
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   │   └── register.blade.php
│   │   │
│   │   ├── invites/
│   │   │   └── index.blade.php
│   │   │
│   │   ├── dashboard.blade.php
│   │   └── welcome.blade.php
│   │
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── console.php
│
├── storage/
│
├── tests/
│
├── vendor/
│
├── .env
├── artisan
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```
---

Your PHP_Laravel12_Invite_Codes Project is now ready!


