<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Freelancer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Freelancer>
 */
class FreelancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>User::all()->random()->id,
            'bio'=>$this->faker->sentence()
           // ,'picture'=>$this->faker->randomElement{['kskajks','skajskja','kkk']}
        ];
    }
}
