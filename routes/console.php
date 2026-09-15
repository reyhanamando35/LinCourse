<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Buat/perbarui akun demo admin yang kredensialnya ditampilkan di halaman login (aman diulang)
Artisan::command('lincourse:demo-admin', function () {
    $user = User::updateOrCreate(
        ['email' => config('app.demo_admin_email')],
        ['name' => 'Demo Admin', 'password' => 'password123']
    );
    Admin::firstOrCreate(['user_id' => $user->id]);

    $this->info("Demo admin siap: {$user->email} / password123");
})->purpose('Create or reset the public read-only demo admin account');
