<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Facades\Faq as FaqFacade;
use Flex360\Pilot\Facades\FaqCategory as FaqCategoryFacade;

class FaqController extends Controller
{
    // index view shows this individual faq
    public function index()
    {
        $faqsWithoutCategory = FaqFacade::doesntHave('faq_categories')
                    ->where('status', 30)
                    ->orderBy('question')
                    ->get();


        $faqCategories = FaqCategoryFacade::with('faqs', 'faqs.faq_categories')->orderBy('name')->get();

        // get query string params
        $faqId = request()->input('faq');
        $catId = request()->input('cat');

        mimic([
            'title' => 'FAQ | Frequently Asked Questions | Furniture Questions',
            'meta_description' => 'Any questions you have about furniture, furniture prices, furniture warranties, or problems with your furniture, we have a questions already answered.'
        ]);

        return view('pilot::frontend.faqs.faqIndex', compact('faqsWithoutCategory', 'faqCategories', 'faqId', 'catId'));
    }
    
    // detail view shows this individual faq
    public function detail($id, $slug)
    {
        $faq = FaqFacade::withoutGlobalScope(PublishedScope::class)->find($id);
        
        mimic($faq->question);

        return view('pilot::frontend.faqs.faqDetail', compact('faq'));
    }
}
