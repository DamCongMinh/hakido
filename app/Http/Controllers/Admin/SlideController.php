<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use App\Models\Category;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $slides = Slide::all();
        return view('Admin.content.admin_content', compact('slides', 'categories'));
    }

    public function create()
    {
        return view('Admin.content.slides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description1' => 'nullable|string',
            'description2' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'required|boolean',
        ]);

        $imagePath = $request->file('image')->store('uploads/slides', 'public');

        Slide::create([
            'title' => $request->title,
            'description1' => $request->description1,
            'description2' => $request->description2,
            'image_path' => 'storage/' . $imagePath, // <--- rất quan trọng
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.slides.index')->with('success', 'Thêm slide thành công!');
    }


    public function edit($id)
    {
        $slide = Slide::findOrFail($id);
        return view('admin.content.slides.edit', compact('slide'));
    }

    public function update(Request $request, $id)
    {
        $slide = Slide::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description1' => 'nullable|string',
            'description2' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'title' => $request->title,
            'description1' => $request->description1,
            'description2' => $request->description2,
            'is_active' => $request->is_active,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/slides', 'public');
            $data['image_path'] = 'storage/' . $imagePath;
        }

        $slide->update($data);

        return redirect()->route('admin.slides.index')->with('success', 'Cập nhật slide thành công!');
    }



    public function destroy($id)
    {
        $slide = Slide::findOrFail($id);
        $slide->delete();
        return redirect()->route('admin.slides.index')->with('success', 'Xoá slide thành công!');
    }
}

