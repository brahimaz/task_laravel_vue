<?php

namespace App\Http\Controllers;

use App\Option;
use App\Product;
use App\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Return list of product.
     *
     */
    public function getProduct()
    {   
        $products = Product::with('variants.options')->orderBy('created_at', 'asc')->get();

        return(['products' => $products]);
    }

    /**
     * Function to add new product.
     *
     */
    public function addProduct(Request $request)
    {

        if(isset($request['checkedVariant'])){
            $product = new Product();
        
            $product->title = $request['title'];
            $product->price = $request['price'];
            $product->quantitie = $request['quantity'];
            $product->variant = true;
    
            $product->save();

            foreach ($request['products'] as $key => $variant_product) {
                $variant = new Variant();

                $variant->price = $variant_product['price'];
                $variant->quantitie = $variant_product['quantity'];

                $variant->product()->associate($product);

                $variant->save();
            
                foreach ($variant_product['options'] as $key => $variant_option) {
                    $option = new Option();

                    $option->option = $variant_option['title'];
                    $option->value = $variant_option['value'];

                    $option->variant()->associate($variant);

                    $option->save();
                }
            }
        }else{
            $product = new Product();
        
            $product->title = $request['title'];
            $product->price = $request['price'];
            $product->quantitie = $request['quantitie'];
    
            $product->save();
        }
        
        return([
            'state' => true,
            'message' => 'Product was added with success!'
        ]);
    }
}
