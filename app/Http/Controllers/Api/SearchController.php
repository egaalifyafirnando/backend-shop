<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('title', 'LIKE', '%' . $request->q . '%')
            ->select('id', 'image', 'title', 'slug', 'category_id', 'content', 'weight', 'price', 'discount', 'stock')
            ->orWhere('slug', 'LIKE', '%' . $request->q . '%')
            ->get();

        if (!$request->q == '') {
            return response()->json([
                "response"      => [
                    "status"    => 200,
                    "message"   => "List Data Products"
                ],
                "products" => $products
            ], 200);
        } else {
            return response()->json([
                "response"      => [
                    "status"    => 200,
                    "message"   => "List Data Products"
                ],
                "products" => ''
            ], 200);
        }
    }
}
