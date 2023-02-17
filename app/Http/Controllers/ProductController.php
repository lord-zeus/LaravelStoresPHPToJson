<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public string $file_path;
    public function __construct(){
        $this->file_path = 'public/products.json';
    }
    public function index($pageNumber){
        $products = $this->getProductsFromStore();
        $perPage = 2;
        $offset = ($pageNumber * $perPage) - $perPage;

        $products = collect($products)->sortByDesc('created_at');

        return new LengthAwarePaginator(
            array_slice($products->toArray(), $offset, $perPage, false),
            count($products),
            $perPage,
            $pageNumber,
        );

        return $product_file;
    }

    public function getProductsFromStore(){
        $product_file = Storage::get($this->file_path);
        if(empty($product_file)){
            Storage::put($this->file_path, "[]");
            $product_file = Storage::get($this->file_path);
        }
        return json_decode($product_file);
    }

    public function rule(Request $request){
        return $this->validate($request, [
            'name' => 'required',
            'unit_price' => 'required|int|min:1',
            'quantity'  => 'required|int|min:1'
        ]);
    }
    public function store(Request $request){
        $this->rule($request);

        $products = $this->getProductsFromStore();
        $request->merge([
            'id' => Str::uuid()->toString(),
            'created_at' => Carbon::now(),
        ]);
        $new_product = $request->all();
        $products[] = $new_product;
        Storage::put($this->file_path, json_encode($products));
        return $new_product;
    }

    public function update(Request $request, $product_id){
        $this->rule($request);
        $products = $this->getProductsFromStore();
        foreach ($products as $k =>  $product){
            Log::debug($k);
            if($product->id === $product_id){
                $product->name = $request->name;
                $product->quantity = $request->quantity;
                $product->unit_price = $request->unit_price;
                $products[$k] = $product;
                Storage::put($this->file_path, json_encode($products));
                return $product;

            }
        }
    }
}
