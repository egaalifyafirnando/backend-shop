<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // FETCH REST API
        $response = Http::withHeaders([
            // API KEY RAJAONGKIR
            'key' => config('services.rajaongkir.key')
        ])->get('https://api.rajaongkir.com/starter/city');

        // LOOP DATA FROM REST API
        foreach ($response['rajaongkir']['results'] as $city) {
            // INSERT TO TABLE 'CITIES'
            City::create([
                'province_id' => $city['province_id'],
                'city_id'     => $city['city_id'],
                'name'        => $city['city_name'] . ' - ' . '(' . $city['type'] . ')',
            ]);
        }
    }
}
