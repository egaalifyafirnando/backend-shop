<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET PRODUCTS ORDER BY LATEST
        $products = Product::latest()->get();

        // RETURN RESPONSE JSON
        return response()->json([
            'success'   => true,
            'message'   => 'List Data Products',
            'products'  => $products
        ], 200);
    }

    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        // GET PRODUCT BY SLUG
        $product = Product::where('slug', $slug)->first();

        // RETURN RESPONSE JSON
        if ($product) {
            return response()->json([
                'success'   => true,
                'message'   => 'Detail Data Product',
                'product'   => $product
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'message'   => 'Data Product Tidak Ditemukan'
            ], 404);
        }
    }
}
