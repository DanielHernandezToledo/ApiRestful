<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {
                
        $this->middleware('auth:api')->except(['index']);
        $this->middleware('client.credentials')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    public function update(Request $request, Product $product, Category $category)
    {
        $product->categories()
        ->syncWithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }

    public function destroy(Product $product, Category $category)
    {
        if(!$product->categories()->find($category->id)) {
            return $this->errorResponse('La categoría no pertenece al producto', 404);
        }

        $product->categories()
        ->detach([$category->id]);

        return $this->showAll($product->categories);
    }

}
