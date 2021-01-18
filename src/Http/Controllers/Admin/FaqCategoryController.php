<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\FieldGroup;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\Faq as FaqFacade;
use Flex360\Pilot\Facades\FaqCategory as FaqCategoryFacade;

class FaqCategoryController extends DynamoController
{
    public function getDynamo()
    {
        return Dynamo::make(get_class(FaqCategoryFacade::getFacadeRoot()))
                    ->auto()
                    ->addIndexButton(function() {
                        return '<a href="/pilot/faq?view=published" class="btn btn-primary btn-sm">Back to FAQs</a>';
                    })
                    ->text('name', [
                        'class' => 'category-name-for-delete-modal',
                    ])
                    ->hideDelete()
                    ->removeBoth('deleted_at')
                    ->hasMany('faqs', [
                            'options' => FaqFacade::all()->pluck('question', 'id'),
                            'label' => 'FAQs',
                            'class' => 'category-dual-list',
                            'id' => 'category-dual-list',
                            'tooltip' => 'Select the FAQs you would like to belong to this category.',
                        ])
                    ->addFormHeaderButton(function() {
                        return '<a href="/pilot/faqcategory" class="btn btn-info btn-sm">Back to FAQ Categories</a>';
                    })
                    ->addFormHeaderButton(function() {
                        return '<a href="/pilot/faq?view=published" class="btn btn-primary btn-sm">Back to FAQs</a>';
                    })
                    ->setFormPanelTitle("Add FAQ Category")
                    ->setSaveItemText('Save FAQ Category')

                    //set index view
                    ->setIndexPanelTitle("FAQ Category Manager")
                    ->setAddItemText('Add FAQ Category')
                    ->applyScopes()
                    ->paginate(10)
                    ->addIndex('test', 'Number of FAQ\'s in this category', function($item) {
                        return $item->faqs->count();
                    })
                    ->addIndex('id', 'Order FAQs in this Category',function ($item) {
                        return '<a href="' . route('admin.faqcategory.faqs', ['id' => $item->id]) . '" class="btn btn-success">Order</a>';
                    })
                    // ->addIndex('Permalink', 'PermaLink', function ($item) {
                    //     // return '<a href="' . route('faq.detail', ['faq' => $item->id, 'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $item->question)))]) . '" target="_blank" class="btn btn-primary btn-sm"><i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-placement="left" data-html="true" title="Serves as a permalink copy tool for the question."></i> View</a>';
                    //     return '<a href="' . $item->url() . '" data-clipboard-text="' . $item->url() . '" target="_blank" class="permalink-button btn btn-primary btn-sm"><i style="font-size: 16px; color: black;" class="fas fa-question-circle" data-toggle="tooltip" data-placement="left" data-html="true" title="Serves as a permalink copy tool for this Category."></i> View</a>';
                    // })
                    ->hideDelete()
                    ->addFormFooterButton(function() {
                        return '<a href="/pilot/testing" class="mt-3 btn btn-danger btn" data-toggle="modal" data-target="#relationships-manager-modal">Delete</a>';
                    });
    }

    /**
    * Remove the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        $category = FaqCategoryFacade::find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.faqcategory.index');
    }


    /**
     * Returns a view where admin can see and reorder faqs within this category
     *
     * @return View
     */
    public function faqs($id)
    {
        $faqcategory = FaqCategoryFacade::find($id);
        
        $items = $faqcategory->faqs()->orderBy(config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category.position')->get();

        $dynamo = (new FaqController)->getDynamo();

        return view('pilot::admin.dynamo.faqs.reorder', compact('dynamo', 'items', 'faqcategory'));
    }

    /**
     * Functions runs on 'reorder' of FAQs within this category
     *
     * @return View
     */
    public function reorderFaqsWithinCategory($id)
    {
        $faqcategory = FaqCategoryFacade::find($id);

        $ids = request()->input('ids');

        foreach ($ids as $position => $faqID) {
        $faq = FaqFacade::find($faqID);

        //$staffMember->position = $position;
        $faq->faq_categories()->updateExistingPivot($faqcategory->id, compact('position'));
        //$staffMember->save();
        }

        return $ids;
    }
}
