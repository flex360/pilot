<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Facades\Service as ServiceFacade;
use Flex360\Pilot\Facades\ServiceCategory as ServiceCategoryFacade;

class ServiceController extends Controller
{
    // index view shows this individual product
    public function index()
    {
        $serviceCategories = ServiceCategoryFacade::with('services', 'services.service_categories')->orderBy('name')->get();

        mimic([
            'title' => 'Services',
            'meta_description' => 'Find different services by category.'
        ]);

        return view('pilot::frontend.services.index', compact('serviceCategories'));
    }

    public function categoryIndex($id, $slug)
    {
        $category = ServiceCategoryFacade::find($id);

        mimic($category->name);

        return view('pilot::frontend.services.categoryIndex', compact('category'));
    }
    
    // detail view shows this individual service
    public function detail($id, $slug)
    {
        $service = ServiceFacade::find($id);
        
        mimic($service->title);

        return view('pilot::frontend.services.detail', compact('service'));
    }
    
}
