<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Facades\Product as ProductFacade;
use Flex360\Pilot\Facades\ProductCategory as ProductCategoryFacade;

class ProductController extends Controller
{
    // index view shows this individual product
    public function index()
    {
        $productCategories = ProductCategoryFacade::with('products', 'products.product_categories')->orderBy('title')->get();

        mimic([
            'title' => 'Products',
            'meta_description' => 'Find different products by category.'
        ]);

        return view('pilot::frontend.products.index', compact('productCategories'));
    }

    public function categoryIndex($id, $slug)
    {
        $category = ProductCategoryFacade::find($id);

        mimic($category->title);

        return view('pilot::frontend.products.categoryIndex', compact('category'));
    }
    
    // detail view shows this individual product
    public function detail($id, $slug)
    {
        $product = ProductFacade::find($id);
        
        mimic($product->name);

        return view('pilot::frontend.products.detail', compact('product'));
    }
    
}
