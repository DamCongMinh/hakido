<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowListProductController extends Controller
{
    private function getProvinces()
    {
        $response = Http::get('https://provinces.open-api.vn/api/p/');
        return $response->successful() ? $response->json() : [];
    }

    private function paginateCollection($items, $perPage = 10)
    {
        $page = request()->get('page', 1);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    private function loadProducts($category_id = null)
    {
        $foodQuery = Food::with('category')
            ->where('is_active', 1)
            ->where('is_approved', 1);
        $beverageQuery = Beverage::with(['category', 'beverageSizes'])
            ->where('is_active', 1)
            ->where('is_approved', 1);

        if ($category_id) {
            $foodQuery->where('category_id', $category_id);
            $beverageQuery->where('category_id', $category_id);
        }

        $foods = $foodQuery->select('id', 'name', 'image', 'old_price', 'discount_percent', 'category_id')->get()
            ->map(function ($item) {
                $item->rating = rand(35, 50) / 10;
                $item->type = 'food';
                $item->new_price = $item->old_price * (1 - ($item->discount_percent ?? 0) / 100);
                return $item;
            });

        $beverages = $beverageQuery->select('id', 'name', 'image', 'category_id')->get()
            ->map(function ($item) {
                $item->rating = rand(35, 50) / 10;
                $item->type = 'beverage';
                $size = $item->beverageSizes->sortBy('old_price')->first();
                $item->old_price = $size?->old_price ?? 0;
                $item->discount_percent = $size?->discount_percent ?? 0;
                $item->new_price = $size ? ($size->old_price * (1 - ($size->discount_percent ?? 0) / 100)) : 0;
                return $item;
            });

        return $foods->concat($beverages)->sortByDesc('id')->values();
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'food'); // default là food
        $perPage = 10;
        
        if ($type === 'food') {
            $products = Food::with('category')
                ->where('is_active', 1)
                ->where('is_approved', 1)
                ->select('id', 'name', 'image', 'old_price', 'discount_percent', 'category_id')
                ->orderByDesc('id')
                ->paginate($perPage)
                ->through(function ($item) {
                    $item->rating = rand(35, 50) / 10;
                    $item->type = 'food';
                    $item->new_price = $item->old_price * (1 - ($item->discount_percent ?? 0) / 100);
                    return $item;
                });

        } elseif ($type === 'beverage') {
            $products = Beverage::with(['category', 'beverageSizes'])
                ->where('is_active', 1)
                ->where('is_approved', 1)
                ->select('id', 'name', 'image', 'category_id')
                ->orderByDesc('id')
                ->paginate($perPage)
                ->through(function ($item) {
                    $item->rating = rand(35, 50) / 10;
                    $item->type = 'beverage';
                    $size = $item->beverageSizes->sortBy('old_price')->first();
                    $item->old_price = $size?->old_price ?? 0;
                    $item->discount_percent = $size?->discount_percent ?? 0;
                    $item->new_price = $size ? ($size->old_price * (1 - ($size->discount_percent ?? 0) / 100)) : 0;
                    return $item;
                });
        } else {
            // fallback hoặc redirect nếu cần
            $products = collect();
        }

        $provinces = $this->getProvinces();

        return view('web.list-product', [
            'products' => $products,
            'provinces' => $provinces,
        ]);
    }


    public function byCategory($category_id)
    {
        $foods = Food::with('category')
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->where('category_id', $category_id)
            ->select('id', 'name', 'image', 'old_price', 'discount_percent', 'category_id')
            ->get()
            ->map(function ($item) {
                $item->rating = rand(35, 50) / 10;
                $item->type = 'food';
                $item->new_price = $item->old_price * (1 - ($item->discount_percent ?? 0) / 100);
                return $item;
            });
    
        $beverages = Beverage::with(['category', 'beverageSizes'])
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->where('category_id', $category_id)
            ->select('id', 'name', 'image', 'category_id')
            ->get()
            ->map(function ($item) {
                $item->rating = rand(35, 50) / 10;
                $item->type = 'beverage';
                $size = $item->beverageSizes->sortBy('old_price')->first();
                $item->old_price = $size?->old_price ?? 0;
                $item->discount_percent = $size?->discount_percent ?? 0;
                $item->new_price = $size ? ($size->old_price * (1 - ($size->discount_percent ?? 0) / 100)) : 0;
                return $item;
            });
    
        $products = $foods->concat($beverages)->sortByDesc('id')->values();
        $paginatedProducts = $this->paginateCollection($products, 10);
    
        $provinces = $this->getProvinces();
    
        return view('web.list-product', [
            'products' => $paginatedProducts,
            'provinces' => $provinces,
        ]);
    }
    
    
}
