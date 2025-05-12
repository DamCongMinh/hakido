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

    public function index(Request $request)
    {
        $type = $request->get('type', 'food');
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

                    $prices = $item->beverageSizes->map(function ($size) {
                        $discount = $size->discount_percent ?? 0;
                        $new_price = $size->old_price * (1 - $discount / 100);
                        return (object)[
                            'size' => $size->size,
                            'id' => $size->id,
                            'old_price' => $size->old_price,
                            'discount_percent' => $discount,
                            'new_price' => $new_price,
                        ];
                    });

                    if ($prices->isNotEmpty()) {
                        $item->min_old_price = $prices->min('old_price');
                        $item->max_old_price = $prices->max('old_price');
                        $item->min_new_price = $prices->min('new_price');
                        $item->max_new_price = $prices->max('new_price');

                        $bestSize = $prices->sortBy('new_price')->first();
                        $item->best_size = $bestSize->size;
                        $item->best_size_id = $bestSize->id;
                    } else {
                        $item->min_old_price = $item->max_old_price = 0;
                        $item->min_new_price = $item->max_new_price = 0;
                        $item->best_size = null;
                        $item->best_size_id = null;
                    }

                    return $item;
                });

        } else {
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

                $prices = $item->beverageSizes->map(function ($size) {
                    $discount = $size->discount_percent ?? 0;
                    $new_price = $size->old_price * (1 - $discount / 100);
                    return (object)[
                        'size' => $size->size,
                        'id' => $size->id,
                        'old_price' => $size->old_price,
                        'discount_percent' => $discount,
                        'new_price' => $new_price,
                    ];
                });

                if ($prices->isNotEmpty()) {
                    $item->min_old_price = $prices->min('old_price');
                    $item->max_old_price = $prices->max('old_price');
                    $item->min_new_price = $prices->min('new_price');
                    $item->max_new_price = $prices->max('new_price');

                    $bestSize = $prices->sortBy('new_price')->first();
                    $item->best_size = $bestSize->size;
                    $item->best_size_id = $bestSize->id;
                } else {
                    $item->min_old_price = $item->max_old_price = 0;
                    $item->min_new_price = $item->max_new_price = 0;
                    $item->best_size = null;
                    $item->best_size_id = null;
                }

                return $item;
            });

        $products = $foods->concat($beverages)->sortByDesc('id')->values();
        $paginatedProducts = $this->paginateCollection($products, 8);
        $provinces = $this->getProvinces();

        return view('web.list-product', [
            'products' => $paginatedProducts,
            'provinces' => $provinces,
        ]);
    }
}
