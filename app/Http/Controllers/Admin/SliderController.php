<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET SLIDERS ORDER BY LATEST
        $sliders = Slider::latest()->paginate(10);

        // RETURN VIEW WITH PASSING DATA SLIDERS
        return view('admin.slider.index', compact('sliders'));
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
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'link'  => 'required'
        ]);

        // UPLOAD IMAGE TO STORAGE
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        // SAVE TO DB
        $slider = Slider::create([
            'image'  => $image->hashName(),
            'link'   => $request->link
        ]);

        // REDIRECT MESSAGE
        if ($slider) {
            // MESSAGE WITH SUCCESS MESSAGE
            return redirect()->route('admin.slider.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            // REDIRECT WITH FAILED MESSAGE
            return redirect()->route('admin.slider.index')->with(['error' => 'Data Gagal Disimpan!']);
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
        // GET SLIDER BY ID
        $slider = Slider::findOrFail($id);

        // DELETE IMAGE ON STORAGE
        $image = Storage::disk('local')->delete('public/sliders/' . basename($slider->image));

        // DELETE SLIDER ON DB
        $slider->delete();

        // RESPONSE JSON
        if ($slider) {
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
