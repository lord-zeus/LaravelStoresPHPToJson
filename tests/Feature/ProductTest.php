<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_product_stored(): void
    {
        $file_path ='public/products.json';
        Storage::delete($file_path);
        $response = $this->post('/api/products', ['quantity' => 1, 'unit_price' => 2, 'name' => 'computers']);
        $response->assertStatus(200);


        $product_file = Storage::get($file_path);
        $products = json_decode($product_file);
        $this->assertEquals('computers',$products[0]->name);

    }

    public function test_product_is_properly_validated(): void{
        $response = $this->post('/api/products', ['quantity' => 1, 'unit_price' => '', 'name' => 'computers']);
        $response->assertStatus(422);
    }
}
