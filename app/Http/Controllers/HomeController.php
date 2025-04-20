<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::where('is_active', true)->get();

        // Convert collection thành mảng slide sạch sẽ
        $slidesData = $slides->map(function ($slide) {
            return [
                'title' => $slide->title,
                'description1' => $slide->description1,
                'description2' => $slide->description2,
                'image' => asset($slide->image_path),
            ];
        })->toArray(); // rất quan trọng!

        return view('web.home', compact('slides', 'slidesData'));
    }

}

