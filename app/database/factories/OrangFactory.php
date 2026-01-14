<?php

namespace Database\Factories;

use App\Models\Orang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orang>
 */
class OrangFactory extends Factory
{
    protected $model = Orang::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake('id_ID')->randomElement(['L', 'P']);
        $firstName = $gender === 'L'
            ? fake('id_ID')->firstNameMale()
            : fake('id_ID')->firstNameFemale();
        $lastName = fake('id_ID')->lastName();

        return [
            'nik' => fake('id_ID')->unique()->nik(),
            'nama' => $firstName.' '.$lastName,
            'gender' => $gender,
            'tanggal_lahir' => fake()->dateTimeBetween('-70 years', '-17 years'),
            'tempat_lahir' => fake('id_ID')->city(),
            'nama_ibu_kandung' => fake('id_ID')->firstNameFemale().' '.fake('id_ID')->lastName(),
            'no_whatsapp' => fake('id_ID')->phoneNumber(),
            'alamat_ktp_id' => null,
            'kartu_keluarga_id' => null,
        ];
    }
}
