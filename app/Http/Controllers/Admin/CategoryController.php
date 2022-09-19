<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET CATEGORIES ORDER BY LATEST
        $categories = Category::latest()->when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->paginate(10);

        // RETURN VIEW AND PASSING DATA 'CATEGORIES'
        return view('admin.category.index', compact('categories'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        // RETURN VIEW CREATE
        return view('admin.category.create');
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
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'name' => 'required|unique:categories'
        ]);

        // UPLOAD IMAGE TO STORAGE
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // SAVE TO DB
        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        // REDIRECT WITH MESSAGE STATUS
        if ($category) {
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * edit
     *
     * @param  mixed $category
     * @return void
     */
    public function edit(Category $category)
    {
        // RETURN VIEW EDIT AND PASSING DATA 'CATEGORY'
        return view('admin.category.edit', compact('category'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $category
     * @return void
     */
    public function update(Request $request, Category $category)
    {
        // VALIDATION RULES
        $this->validate($request, [
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        // CHECK IF IMAGE IS NULL
        if ($request->file('image') == '') {
            // UPDATE DATA WITHOUT IMAGE

            // FIND CATEGORY BY ID
            $category = Category::findOrFail($category->id);
            // UPDATE THIS CATEGORY
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-')
            ]);
        } else {
            // UPDATE WITH IMAGE

            // DELETE OLD IMAGE
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            // UPLOAD NEW IMAGE FROM REQUEST
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            // FIND CATEGORY BY ID
            $category = Category::findOrFail($category->id);
            // UPDATE THIS CATEGORY
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-')
            ]);
        }

        // REDIRECT WITH MESSAGE STATUS
        if ($category) {
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } else {
            return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Diperbarui!']);
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
        // FIND CATEGORY BY ID
        $category = Category::findOrFail($id);

        // DELETE IMAGE FILE FROM STORAGE
        $image = Storage::disk('local')->delete('public/categories/' . basename($category->image));

        // LOOP PRODUCT, AND THEN DELETE TOO (BUG SOLVED)
        foreach ($category->products()->get() as $child) {
            $child->delete();
        }

        // DELETE CATEGORY FROM DB
        $category->delete();

        // REDIRECT WITH MESSAGE STATUS
        if ($category) {
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
