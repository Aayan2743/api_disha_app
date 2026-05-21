<?php
namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users first (clients & appointments reference users via added_by)
        $admin = User::factory()->create([
            'name'     => 'Admin User',
            'email'    => 'admin@example.com',
            'phone'    => '9999999999',
            'role'     => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name'     => 'Telecaller User',
            'email'    => 'telecaller@example.com',
            'phone'    => '8888888888',
            'role'     => 'telecaller',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name'     => 'Receptionist User',
            'email'    => 'receptionist@example.com',
            'phone'    => '7777777777',
            'role'     => 'receptionist',
            'password' => bcrypt('password'),
        ]);

        // Seed Clients
        Client::factory(50)->create([
            'added_by' => $admin->id,
        ]);

        // Seed Appointments
        Appointment::factory(30)->create([
            'added_by' => $admin->id,
        ]);
    }
}
