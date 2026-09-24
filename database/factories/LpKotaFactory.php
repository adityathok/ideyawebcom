<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LpKota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LpKota>
 */
final class LpKotaFactory extends Factory
{
    protected $model = LpKota::class;

    public function definition(): array
    {
        $kota = fake()->randomElement(['Surakarta', 'Sukoharjo', 'Karanganyar', 'Boyolali', 'Klaten', 'Wonogiri']);
        $kecamatan = 'Kec. '.rtrim(fake()->citySuffix().' '.fake()->lastName(), '.');

        return [
            'nama_kota' => $kota,
            'nama_kecamatan' => $kecamatan,
            'deskripsi' => fake()->paragraph(),
            'gambar_utama' => null,
            'gambar_icon' => null,
        ];
    }

    /**
     * Nama wilayah yang pasti, dipakai tes yang butuh data terbaca.
     */
    public function wilayah(string $kota, string $kecamatan): static
    {
        return $this->state(fn (array $a): array => [
            'nama_kota' => $kota,
            'nama_kecamatan' => $kecamatan,
        ]);
    }
}
