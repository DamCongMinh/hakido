<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;

class HomeController extends Controller
{
    public function index()
    {
        $slidesData = Slide::where('is_active', 1)->get()->map(function ($slide) {
            // Thêm thuộc tính image chứa đường dẫn đầy đủ
            $slide->image = asset($slide->image_path);
            return $slide;
        });

        return view('web.home', compact('slidesData'));
    }
}
