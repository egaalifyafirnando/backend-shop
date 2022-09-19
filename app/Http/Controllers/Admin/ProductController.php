<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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
        $products = Product::latest()->when(request()->q, function ($products) {
            $products = $products->where('title', 'like', '%' . $products . '%');
        })->paginate(10);

        // RETURN VIEW WITH PASSING DATA PRODUCTS
        return view('admin.product.index', compact('products'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        // GET CATEGORY RELATED WITH PRODUCT
        $categories = Category::latest()->get();

        // RETURN VIEW WITH PASSING DATA CATEGORIES
        return view('admin.product.create', compact('categories'));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // VALIDATION RULES
        $this->validate($request, [
            'image'          => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'          => 'required|unique:products',
            'category_id'    => 'required',
            'content'        => 'required',
            'weight'         => 'required',
            'price'          => 'required',
            'discount'       => 'required',
        ]);

        // UPLOAD IMAGE TO STORAGE
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // SAVE TO DB
        $product = Product::create([
            'image'          => $image->hashName(),
            'title'          => $request->title,
            'slug'           => Str::slug($request->title, '-'),
            'category_id'    => $request->category_id,
            'content'        => $request->content,
            'unit'           => $request->unit,
            'weight'         => $request->weight,
            'price'          => $request->price,
            'discount'       => $request->discount,
            'keywords'       => $request->keywords,
            'description'    => $request->description,
        ]);

        if ($product) {
            // REDIRECT WITH SUCCESS MESSAGE
            return redirect()->route('admin.product.index')->with(['success' => 'Data Berhasil Ditambahkan!']);
        } else {
            // REDIRECT WITH FAILED MESSAGE
            return redirect()->route('admin.product.index')->with(['error' => 'Data Gagal Ditambahkan!']);
        }
    }

    /**
     * edit
     *
     * @return void
     */
    public function edit(Product $product)
    {
        // GET DATA CATEGORY RELATED WITH PRODUCT
        $categories = Category::latest()->get();

        // RETURN VIEW WITH PASSING DATA PRODUCT AND CATEGORIES
        return view('admin.product.edit', compact('product', 'categories'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $product
     * @return void
     */
    public function update(Request $request, Product $product)
    {
        // VALIDATION RULES
        $this->validate($request, [
            'title'          => 'required|unique:products,title,' . $product->id,
            'category_id'    => 'required',
            'content'        => 'required',
            'weight'         => 'required',
            'price'          => 'required',
            'discount'       => 'required',
        ]);

        // CHECK IF IMAGE IS NULL(?)
        if ($request->file('image') == '') {
            // UPDATE WITHOUT IMAGE
            // get data product by id
            $product = Product::findOrFail($product->id);
            $product->update([
                'title'          => $request->title,
                'slug'           => Str::slug($request->title, '-'),
                'category_id'    => $request->category_id,
                'content'        => $request->content,
                'unit'           => $request->unit,
                'weight'         => $request->weight,
                'price'          => $request->price,
                'discount'       => $request->discount,
                'keywords'       => $request->keywords,
                'description'    => $request->description,
            ]);
        } else {
            // UPDATE WITH IMAGE
            // delete old image
            Storage::disk('local')->delete('public/products/' . basename($product->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            // UPDATE WITH IMAGE
            $product = Product::findOrFail($product->id);
            $product->update([
                'image'          => $image->hashName(),
                'title'          => $request->title,
                'slug'           => Str::slug($request->title, '-'),
                'category_id'    => $request->category_id,
                'content'        => $request->content,
                'unit'           => $request->unit,
                'weight'         => $request->weight,
                'price'          => $request->price,
                'discount'       => $request->discount,
                'keywords'       => $request->keywords,
                'description'    => $request->description,
            ]);
        }

        if ($product) {
            // REDIRECT WITH SUCCESS MESSAGE
            return redirect()->route('admin.product.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } else {
            // REDIRECT WITH FAILED MESSAGE
            return redirect()->route('admin.product.index')->with(['error' => 'Data Gagal Diperbarui!']);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        // GET PRODUCT BY ID
        $product = Product::findOrFail($id);

        // DELETE IMAGE FROM STORAGE
        $image = Storage::disk('local')->delete('public/products/' . basename($product->image));

        // DELETE PRODUCT
        $product->delete();

        // RETURN RESPONSE STATUS
        if ($product) {
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
