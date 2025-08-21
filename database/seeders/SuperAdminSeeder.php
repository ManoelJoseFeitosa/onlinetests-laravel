<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nome' => 'Super Admin',
            'email' => 'manoelbd2012@gmail.com',
            'password' => Hash::make('Mf@871277'), // A senha será criptografada
            'role' => 'coordenador', // Como no seu código original
            'is_superadmin' => true,
            'precisa_trocar_senha' => false, // O super admin não precisa trocar
            'escola_id' => null, // Super admin não pertence a nenhuma escola
        ]);
    }
}