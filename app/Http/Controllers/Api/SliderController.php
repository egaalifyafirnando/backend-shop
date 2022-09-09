<?php

namespace App\Http\Controllers\Api;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SliderController extends Controller
{
    public function index()
    {
        // GET SLIDER ORDER BY LATEST
        $sliders = Slider::latest()->get();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'List Data Slider',
            'sliders'   => $sliders
        ]);
    }
}
