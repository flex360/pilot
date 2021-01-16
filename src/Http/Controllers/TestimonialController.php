<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Facades\Testimonial as TestimonialFacade;
use Flex360\Pilot\Facades\Page as PageFacade;

class TestimonialController extends Controller
{
    /**
     * Load /testimonials page;
     *
     * @return View
     */
    public function index()
    {
        //get all resource categories, order by name
        $testimonials = TestimonialFacade::orderBy('name')->get();

        PageFacade::mimic([
            'title' => 'Testimonials'
        ]);

        return view('pilot::frontend.testimonials.index', compact('testimonials'));
    }
}
