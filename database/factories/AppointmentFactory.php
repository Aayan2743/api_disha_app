<?php
namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientType  = fake()->randomElement(['new_client', 'existing_client']);
        $clientId    = null;
        $clientName  = null;
        $clientPhone = null;

        if ($clientType === 'existing_client') {
            $client = Client::inRandomOrder()->first();
            if ($client) {
                $clientId    = $client->id;
                $clientName  = $client->fullname;
                $clientPhone = $client->phone;
            } else {
                // Fallback to new client if no clients exist yet
                $clientType  = 'new_client';
                $clientName  = fake()->name();
                $clientPhone = fake()->numerify('9#########');
            }
        } else {
            $clientName  = fake()->name();
            $clientPhone = fake()->numerify('9#########');
        }

        $apptDate = fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d');
        $apptTime = fake()->numberBetween(9, 18) . ':' . str_pad(fake()->numberBetween(0, 3) * 15, 2, '0', STR_PAD_LEFT) . ':00';

        return [
            'appointment_type' => fake()->randomElement(['online', 'offline']),
            'client_type'      => $clientType,
            'client_id'        => $clientId,
            'client_name'      => $clientName,
            'client_phone'     => $clientPhone,
            'appointment_date' => $apptDate,
            'appointment_time' => $apptTime,
            'fee_amount'       => fake()->randomFloat(2, 500, 10000),
            'payment_method'   => fake()->randomElement(['cash', 'online_payment']),
            'remarks'          => fake()->optional(0.5)->sentence(),
            'added_by'         => User::inRandomOrder()->value('id') ?? User::factory(),
            'status'           => fake()->randomElement([1, 2, 3]),
        ];
    }

    /**
     * Indicate that the appointment is booked.
     */
    public function booked(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 2,
        ]);
    }

    /**
     * Indicate that the appointment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 3,
        ]);
    }
}
