<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use App\Models\Departement;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->date(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city_id' => City::all()->random()->id,
            'province_id' => Province::all()->random()->id,
            'country_id' => Country::all()->random()->id,
            'departement_id' => Departement::all()->random()->id

        ];
    }
}
