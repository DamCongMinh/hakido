<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Beverage;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchAndFilterController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $type = $request->input('type'); // thêm type: 'product' hoặc 'restaurant'

        if ($type === 'restaurant') {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(10);
            return view('web.list-restaurant', [
                'restaurants' => $restaurants,
                'keyword' => $keyword
            ]);
        }

        // Nếu không phải tìm nhà hàng thì mặc định là tìm sản phẩm
        $foods = Food::where('name', 'like', "%{$keyword}%")->get();
        $beverages = Beverage::where('name', 'like', "%{$keyword}%")->get();
        $products = $foods->concat($beverages);

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

        $provinces = $this->getProvinces();

        return view('web.list-product', [
            'products' => $paginatedProducts,
            'keyword' => $keyword,
            'provinces' => $provinces
        ]);
    }



    public function suggestions(Request $request)
    {
        $keyword = $request->input('keyword');
        
        if (empty($keyword)) {
            return response()->json([]);
        }

        $words = explode(' ', $keyword);

        // Foods
        $foods = Food::select('id', 'name')
            ->where('is_active', 1)
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', '%' . $word . '%');
                }
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => 'food'
                ];
            });

        // Beverages
        $beverages = Beverage::select('id', 'name')
            ->where('is_active', 1)
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', '%' . $word . '%');
                }
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => 'beverage'
                ];
            });

        // Restaurants
        $restaurants = \App\Models\Restaurant::select('id', 'name')
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', '%' . $word . '%');
                }
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => 'restaurant'
                ];
            });

        $results = $foods->merge($beverages)->merge($restaurants)->take(5)->values();

        return response()->json($results);
    }



    public function index()
    {
        $foods = Food::where('is_active', 1)->get();
        $beverages = Beverage::where('is_active', 1)->get();
        $provinces = $this->getProvinces();

        return view('web.list-product', compact('foods', 'beverages', 'provinces'));
    }

    public function filter(Request $request)
    {
           
        $provinceName = $this->getProvinceNameById($request->province);
        $districtName = $this->getDistrictNameById($request->district);
        $wardName = $this->getWardNameById($request->ward);

        // ------------------- LỌC FOODS -------------------
        $foodsQuery = Food::with('restaurant')
            ->where('is_active', 1)
            ->whereHas('restaurant', function ($q) use ($provinceName, $districtName, $wardName) {
                $q->where(function ($subQuery) use ($provinceName, $districtName, $wardName) {
                    if ($provinceName) $subQuery->where('address', 'like', "%{$provinceName}%");
                    if ($districtName) $subQuery->where('address', 'like', "%{$districtName}%");
                    if ($wardName) $subQuery->where('address', 'like', "%{$wardName}%");
                });
            });

        if ($request->price) {
            $foodsQuery->where('old_price', '<=', $request->price);
        }

        $foods = $foodsQuery->select('id', 'name', 'image', 'old_price', 'discount_percent', 'restaurant_id')
            ->get()
            ->map(function ($item) {
                $item->rating = rand(35, 50) / 10;
                $item->type = 'food';
                $item->address = optional($item->restaurant)->address ?? '';
                $item->base_price = $item->old_price;
                $item->discount_percent = $item->discount_percent ?? 0;
                $item->new_price = $item->base_price * (1 - $item->discount_percent / 100);
                return $item;
            });

        // ------------------- LỌC BEVERAGES -------------------
        $beveragesQuery = Beverage::with(['restaurant', 'beverageSizes' => function ($query) use ($request) {
                if ($request->price) {
                    $query->where('old_price', '<=', $request->price);
                }
            }])
            ->where('is_active', 1)
            ->whereHas('restaurant', function ($q) use ($provinceName, $districtName, $wardName) {
                $q->where(function ($subQuery) use ($provinceName, $districtName, $wardName) {
                    if ($provinceName) $subQuery->where('address', 'like', "%{$provinceName}%");
                    if ($districtName) $subQuery->where('address', 'like', "%{$districtName}%");
                    if ($wardName) $subQuery->where('address', 'like', "%{$wardName}%");
                });
            });

        if ($request->price) {
            $beveragesQuery->whereHas('beverageSizes', function ($query) use ($request) {
                $query->where('old_price', '<=', $request->price);
            });
        }

        $beverages = $beveragesQuery->select('id', 'name', 'image', 'restaurant_id')
            ->get()
            ->map(function ($item) {
                $item->rating = rand(35, 50) / 10;
                $item->type = 'beverage';
                $item->address = optional($item->restaurant)->address ?? '';

                $minSize = $item->beverageSizes->sortBy('old_price')->first();
                if ($minSize) {
                    $item->base_price = $minSize->old_price;
                    $item->discount_percent = $minSize->discount_percent ?? 0;
                    $item->new_price = $item->base_price * (1 - $item->discount_percent / 100);
                } else {
                    $item->base_price = 0;
                    $item->discount_percent = 0;
                    $item->new_price = 0;
                }

                return $item;
            });

        // ------------------- GỘP FOODS + BEVERAGES -------------------
        $products = $foods->concat($beverages)->sortByDesc('id')->values();

        if ($request->type && $request->type !== 'all') {
            $products = $products->where('type', $request->type)->values();
        }

        // ------------------- PHÂN TRANG -------------------
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $provinces = $this->getProvinces();

        return view('web.list-product', [
            'products' => $paginatedProducts,
            'provinces' => $provinces,
        ]);
    }

    // ------------------- CÁC HÀM PHỤ TRỢ -------------------
    private function getProvinces()
    {
        $response = Http::get('https://provinces.open-api.vn/api/p/');
        if ($response->successful()) {
            return $response->json(); 
        }
        return [];
    }

    private function getProvinceNameById($provinceId)
    {
        if ($provinceId == 'all') return null;

        $response = Http::get('https://provinces.open-api.vn/api/p/' . $provinceId);
        if ($response->successful()) {
            return $response->json()['name'] ?? null;
        }
        return null;
    }

    private function getDistrictNameById($districtId)
    {
        if ($districtId == 'all') return null;

        $response = Http::get('https://provinces.open-api.vn/api/d/' . $districtId);
        if ($response->successful()) {
            return $response->json()['name'] ?? null;
        }
        return null;
    }

    private function getWardNameById($wardId)
    {
        if ($wardId == 'all') return null; 

        $response = Http::get('https://provinces.open-api.vn/api/w/' . $wardId);
        if ($response->successful()) {
            return $response->json()['name'] ?? null;
        }
        return null;
    }
}
