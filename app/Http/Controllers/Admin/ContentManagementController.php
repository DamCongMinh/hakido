<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;

class ContentManagementController extends Controller
{
    public function index()
    {
        $slides = Slide::all();
        $categories = Category::all();

        return view('Admin.content.admin_content', compact('slides', 'categories'));
    }
}
