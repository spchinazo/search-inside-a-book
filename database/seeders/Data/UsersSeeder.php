<?php

namespace Database\Seeders\Data;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Edw Rys',
                'email' => 'edw-toni@hotmail.com',
                'password' => bcrypt('hola1234'),
            ],
        ];
        foreach ($users as $user) {
            if (User::where('email', $user['email'])->exists()) {
                continue;
            }
            User::create($user);
        }
    }
}
