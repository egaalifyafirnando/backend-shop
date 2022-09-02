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
        // get categories order by latest
        $categories = Category::latest()->when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->paginate(10);

        // return view and passing data 'categories'
        return view('admin.category.index', compact('categories'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        // return view create
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
        // validation rules
        $this->validate($request, [
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
            'name' => 'required|unique:categories'
        ]);

        // upload image to storage
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // save to DB
        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            // redirect with success message
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            // redirect with failed message
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
        // return view edit with passing data category
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
        // validation rules
        $this->validate($request, [
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        // check if image is null
        if ($request->file('image') == '') {
            // update data without image
            $category = Category::findOrFail($category->id);
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-')
            ]);
        } else {
            // delete old image
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            // update with new image
            $category = Category::findOrFail($category->id);
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-')
            ]);
        }


        if ($category) {
            // redirect with success message
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Diperbarui!']);
        } else {
            // redirect with failed message
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
        $category = Category::findOrFail($id);
        $image = Storage::disk('local')->delete('public/categories/' . basename($category->image));
        $category->delete();

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
