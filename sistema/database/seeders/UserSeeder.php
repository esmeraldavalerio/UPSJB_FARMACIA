<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        $users = [
            [
                'role'    => 'Administrador',
                'name'    => 'Juan Perez',
                'dni'     => '12345678',
                'phone'   => '987654321',
                'tuition' => null,
                'email'   => 'admin@farmacia.com',
            ],
            [
                'role'    => 'Químico farmacéutico',
                'name'    => 'Maria Lopez',
                'dni'     => '87654321',
                'phone'   => '912345678',
                'tuition' => 'CQFP-12345',
                'email'   => 'farmaceutico@farmacia.com',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'role_id'  => $roles[$user['role']],
                'name'     => $user['name'],
                'dni'      => $user['dni'],
                'phone'    => $user['phone'],
                'tuition'  => $user['tuition'],
                'email'    => $user['email'],
                'password' => bcrypt('password'),
            ]);
        }
    }
}