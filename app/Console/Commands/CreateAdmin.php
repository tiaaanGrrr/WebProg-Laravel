<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('app:create-admin {email?}')]
#[Description('Buat akun admin untuk mengelola produk')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email admin');
        $password = $this->secret('Password admin (minimal 8 karakter)');
        $validator = Validator::make(compact('email', 'password'), [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $user = new User(['name' => 'Admin', 'email' => $email, 'password' => $password]);
        $user->is_admin = true;
        $user->save();
        $this->info('Akun admin berhasil dibuat. Login melalui /admin/login.');

        return self::SUCCESS;
    }
}
