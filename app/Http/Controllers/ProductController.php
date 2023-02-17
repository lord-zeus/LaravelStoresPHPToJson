<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public string $file_path;
    public function __construct(){
        $this->file_path = 'public/products.json';
    }
    public function index(){
        $product_file = Storage::get($this->file_path);
        if(empty($product_file)){
            Storage::put($this->file_path, "[]");
            $product_file = Storage::get($this->file_path);
        }
        return $product_file;
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'unit_price' => 'required|int|min:1',
            'quantity'  => 'required|int|min:1'
        ]);

        $product_file = $this->index();
        $products = json_decode($product_file);
        Log::debug($products);
        return $products;
    }
}
