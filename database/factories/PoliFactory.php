<?php

namespace Database\Factories;

use App\Models\Poli;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poli>
 */
class PoliFactory extends Factory
{
    protected $model = Poli::class;

    public function definition(): array
    {
        return [
            'nama_poli' => fake()->unique()->word(),
            'keterangan' => fake()->sentence(),
            'tarif' => fake()->numberBetween(50000, 200000),
            'kode_poli' => strtoupper(fake()->unique()->lexify('??')),
        ];
    }
}
