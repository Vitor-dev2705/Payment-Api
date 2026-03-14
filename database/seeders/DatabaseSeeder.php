<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Product;
use App\Models\Gateway;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Gateway::updateOrCreate(['name' => 'Gateway 1'], ['priority' => 1, 'is_active' => true]);
        Gateway::updateOrCreate(['name' => 'Gateway 2'], ['priority' => 2, 'is_active' => true]);

        Client::updateOrCreate(
            ['email' => 'anderson@email.com'],
            ['name' => 'Anderson Vitor']
        );

        Product::updateOrCreate(
            ['name' => 'PlayStation 5'],
            ['amount' => 4500.00]
        );

        User::updateOrCreate(
            ['email' => 'admin@betalent.tech'],
            [
                'name' => 'Anderson Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        User::updateOrCreate(
            ['email' => 'vendedor@betalent.tech'],
            [
                'name' => 'Vendedor Teste',
                'password' => Hash::make('password123'),
                'role' => 'seller'
            ]
        );
    }
}
