<?php

namespace Database\Factories;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Factories\Factory;

class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}