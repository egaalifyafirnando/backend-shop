<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Expr\FuncCall;

class RajaOngkirController extends Controller
{
    public function getProvinces()
    {
        // GET ALL PROVINCE
        $provinces = Province::all();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'list Data Provinces',
            'data'      => $provinces
        ]);
    }

    public function getCities(Request $request)
    {
        // GET ALL CITY RELATED BY PROVINCE REQUEST
        $city = City::where('province_id', $request->province_id)->get();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'List Data Cities By Province',
            'data'      => $city
        ]);
    }

    public function checkOngkir(Request $request)
    {
        // FETCH REST API RAJAONGKIR
        $response = Http::withHeaders([
            // API KEY RAJAONGKIR
            'key'   => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost', [
            // SEND DATA
            'origin'        => 409, //ID ORIGIN KABUPATEN SIDOARJO
            'destination'   => $request->city_destination,
            'weight'        => $request->weight,
            'courier'       => $request->courier
        ]);

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'List Data Cost All Courier: ' . $request->courier,
            'data'      => $response['rajaongkir']['results'][0]
        ]);
    }
}
