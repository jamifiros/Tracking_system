<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Destination;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
        

        'user_id' => function () {
            // Associate each destination with a random user
            return User::inRandomOrder()->first()->id;
        },
        'destName' =>$this-> faker->company,
        'contactNo' =>$this-> faker->phoneNumber,
        'Location' =>$this-> faker->address,
        
    ];
    }
}
