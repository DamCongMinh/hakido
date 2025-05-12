<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchAndFilterController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $type = $request->input('type');

        if ($type === 'restaurant') {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(10);

            return view('web.list-restaurant', [
                'restaurants' => $restaurants,
                'keyword' => $keyword,
                'type' => $type,
            ]);
        }

        $foods = Food::where('name', 'like', "%{$keyword}%")
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->get()
            ->map(function ($item) {
                $item->type = 'food';
                $item->discount_percent = $item->discount_percent ?? 0;
                $item->new_price = $item->old_price * (1 - $item->discount_percent / 100);
                return $item;
            });

        $beverages = Beverage::with('beverageSizes')
            ->where('name', 'like', "%{$keyword}%")
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->get()
            ->map(function ($item) {
                $item->type = 'beverage';
                $sizes = $item->beverageSizes;

                if ($sizes->isNotEmpty()) {
                    $minSize = $sizes->sortBy('old_price')->first();
                    $maxSize = $sizes->sortByDesc('old_price')->first();

                    $item->min_old_price = $minSize->old_price ?? 0;
                    $item->max_old_price = $maxSize->old_price ?? 0;

                    $item->min_new_price = $item->min_old_price * (1 - ($minSize->discount_percent ?? 0) / 100);
                    $item->max_new_price = $item->max_old_price * (1 - ($maxSize->discount_percent ?? 0) / 100);

                    $bestSize = $sizes->sortBy(function ($s) {
                        return $s->old_price * (1 - ($s->discount_percent ?? 0) / 100);
                    })->first();

                    $item->best_size = $bestSize->size ?? null;
                } else {
                    $item->min_old_price = 0;
                    $item->max_old_price = 0;
                    $item->min_new_price = 0;
                    $item->max_new_price = 0;
                    $item->best_size = null;
                }

                return $item;
            });

        $products = $foods->concat($beverages)->sortByDesc('id')->values();

        $perPage = 10;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $paginatedProducts = new LengthAwarePaginator(
            $products->slice($offset, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('web.list-product', [
            'products' => $paginatedProducts,
            'keyword' => $keyword,
            'provinces' => $this->getProvinces(),
            'type' => $type,
        ]);
    }

    public function suggestions(Request $request)
    {
        $keyword = $request->input('keyword');
        if (empty($keyword)) return response()->json([]);

        $words = explode(' ', $keyword);

        $foods = Food::select('id', 'name')
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            })->limit(5)->get()->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'type' => 'food'
            ]);

        $beverages = Beverage::select('id', 'name')
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            })->limit(5)->get()->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'type' => 'beverage'
            ]);

        $restaurants = Restaurant::select('id', 'name')
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            })->limit(5)->get()->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'type' => 'restaurant'
            ]);

        $results = $foods->merge($beverages)->merge($restaurants)->take(5)->values();

        return response()->json($results);
    }

    public function index()
    {
        $foods = Food::where('is_active', 1)
            ->where('is_approved', 1)
            ->get()->map(function ($item) {
                $item->type = 'food';
                return $item;
            });

        $beverages = Beverage::where('is_active', 1)
            ->where('is_approved', 1)
            ->get()->map(function ($item) {
                $item->type = 'beverage';
                return $item;
            });

        $products = $foods->concat($beverages)->sortByDesc('id')->values();

        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('web.list-product', [
            'products' => $paginatedProducts,
            'provinces' => $this->getProvinces(),
            'categories' => Category::all(),
            'type' => 'all',
        ]);
    }

    public function filter(Request $request)
    {
        $provinceName = $this->getProvinceNameById($request->province);
        $districtName = $this->getDistrictNameById($request->district);
        $wardName = $this->getWardNameById($request->ward);
    
        $foodsQuery = Food::with('restaurant')
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->whereHas('restaurant', function ($q) use ($provinceName, $districtName, $wardName) {
                $q->where(function ($sub) use ($provinceName, $districtName, $wardName) {
                    if ($provinceName) $sub->where('address', 'like', "%{$provinceName}%");
                    if ($districtName) $sub->where('address', 'like', "%{$districtName}%");
                    if ($wardName) $sub->where('address', 'like', "%{$wardName}%");
                });
            });
    
        if ($request->category && $request->category !== 'all') {
            $foodsQuery->where('category_id', $request->category);
        }
    
        if ($request->price) {
            $foodsQuery->where('old_price', '<=', $request->price);
        }
    
        $foods = $foodsQuery->get()->map(function ($item) {
            $item->type = 'food';
            $item->rating = rand(35, 50) / 10;
            $item->address = optional($item->restaurant)->address ?? '';
            $item->discount_percent = $item->discount_percent ?? 0;
            $item->new_price = $item->old_price * (1 - $item->discount_percent / 100);
            return $item;
        });
    
        $beveragesQuery = Beverage::with(['restaurant', 'beverageSizes' => function ($query) use ($request) {
            if ($request->price) {
                $query->where('old_price', '<=', $request->price);
            }
        }])
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->whereHas('restaurant', function ($q) use ($provinceName, $districtName, $wardName) {
                $q->where(function ($sub) use ($provinceName, $districtName, $wardName) {
                    if ($provinceName) $sub->where('address', 'like', "%{$provinceName}%");
                    if ($districtName) $sub->where('address', 'like', "%{$districtName}%");
                    if ($wardName) $sub->where('address', 'like', "%{$wardName}%");
                });
            });
    
        if ($request->category && $request->category !== 'all') {
            $beveragesQuery->where('category_id', $request->category);
        }
    
        $beverages = $beveragesQuery->get()->map(function ($item) {
            $item->type = 'beverage';
            $item->rating = rand(35, 50) / 10;
            $item->address = optional($item->restaurant)->address ?? '';
    
            $sizes = $item->beverageSizes;
            if ($sizes->isNotEmpty()) {
                $minSize = $sizes->sortBy('old_price')->first();
                $maxSize = $sizes->sortByDesc('old_price')->first();
    
                $item->min_old_price = $minSize->old_price ?? 0;
                $item->max_old_price = $maxSize->old_price ?? 0;
    
                $item->min_new_price = $item->min_old_price * (1 - ($minSize->discount_percent ?? 0) / 100);
                $item->max_new_price = $item->max_old_price * (1 - ($maxSize->discount_percent ?? 0) / 100);
    
                $bestSize = $sizes->sortBy(function ($s) {
                    return $s->old_price * (1 - ($s->discount_percent ?? 0) / 100);
                })->first();
    
                $item->best_size = $bestSize->size ?? null;
            } else {
                $item->min_old_price = 0;
                $item->max_old_price = 0;
                $item->min_new_price = 0;
                $item->max_new_price = 0;
                $item->best_size = null;
            }
    
            return $item;
        });
    
        $products = $foods->concat($beverages)->sortByDesc('id')->values();
    
        if ($request->type && $request->type !== 'all') {
            $products = $products->where('type', $request->type)->values();
        }
    
        $perPage = 8;
        $page = $request->get('page', 1);
        $paginatedProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    
        return view('web.list-product', [
            'products' => $paginatedProducts,
            'provinces' => $this->getProvinces(),
            'categories' => Category::all(),
            'type' => $request->type ?? 'all',
        ]);
    }

    private function getProvinces()
    {
        $response = Http::get('https://provinces.open-api.vn/api/p/');
        return $response->successful() ? $response->json() : [];
    }

    private function getProvinceNameById($provinceId)
    {
        if ($provinceId == 'all') return null;
        $response = Http::get("https://provinces.open-api.vn/api/p/{$provinceId}");
        return $response->successful() ? $response->json()['name'] ?? null : null;
    }

    private function getDistrictNameById($districtId)
    {
        if ($districtId == 'all') return null;
        $response = Http::get("https://provinces.open-api.vn/api/d/{$districtId}");
        return $response->successful() ? $response->json()['name'] ?? null : null;
    }

    private function getWardNameById($wardId)
    {
        if ($wardId == 'all') return null;
        $response = Http::get("https://provinces.open-api.vn/api/w/{$wardId}");
        return $response->successful() ? $response->json()['name'] ?? null : null;
    }
}
