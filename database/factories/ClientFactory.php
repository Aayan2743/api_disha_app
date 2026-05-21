<?php
namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'call_type' => fake()->randomElement(['incoming', 'outgoing']),
            'lead_type' => fake()->randomElement(['cold', 'hot', 'warm']),
            'fullname'  => fake()->name(),
            'phone'     => fake()->numerify('9#########'),
            'location'  => fake()->optional(0.7)->city(),
            'referance' => fake()->optional(0.4)->name(),
            'case_type' => fake()->optional(0.6)->randomElement([
                'Civil',
                'Criminal',
                'Family',
                'Property',
                'Corporate',
                'Taxation',
                'Labour',
                'Consumer',
            ]),
            'remarks'   => fake()->optional(0.5)->sentence(),
            'added_by'  => User::inRandomOrder()->value('id') ?? User::factory(),
            'status'    => fake()->randomElement([0, 1]),
        ];
    }

    /**
     * Indicate that the client is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the client is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 0,
        ]);
    }
}
