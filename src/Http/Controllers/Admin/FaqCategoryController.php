<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use App\Http\Requests;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Jzpeepz\Dynamo\FieldGroup;
use App\Http\Controllers\Controller;
use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Facades\Faq as FaqFacade;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\FaqCategory as FaqCategoryFacade;

class FaqCategoryController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(FaqCategoryFacade::getFacadeRoot()));
                        //check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.display_name') != null) {
                            $dynamo->alias(Str::singular(config('pilot.plugins.faqs.children.manage_faq_categories.display_name')));
                        }



                        /************************************************************************************
                         *  Pilot plugin: FAQ Category form view                                           *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->addIndexButton(function() {
                            return '<a href="/pilot/faq?view=published" class="btn btn-primary btn-sm">Back to FAQs</a>';
                        });
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.name', true)) {
                            $dynamo->text('name', [
                                'class' => 'category-name-for-delete-modal',
                            ]);
                        }
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.faq_selector', true)) {
                            $dynamo->hasMany('faqs', [
                                'options' => FaqFacade::withoutGlobalScope(PublishedScope::class)->orderBy('question')->pluck('question', 'id'),
                                'value' => function ($item, $field) {
                                    return $item->faqs()->withoutGlobalScope(PublishedScope::class)->pluck('id')->toArray();
                                },
                                'label' => 'FAQs',
                                'class' => 'category-dual-list',
                                'id' => 'category-dual-list',
                                'tooltip' => 'Select the FAQs you would like to belong to this category.',
                            ]);
                        }
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.status', true)) {
                            $dynamo->select('status', [
                                'options' => FaqFacade::getStatuses(),
                                'help' => 'Use the \'\'Draft\'\' status to save information as you have it. When you\'re ready for a Product Category to
                                              show up on the front end of the website, change it to \'\'Published\'\' and then click the \'\'Save Product Category\'\' button.',
                                'position' => 200,
                            ]);
                        }
                        $dynamo->hideDelete()
                        ->removeBoth('deleted_at')
                        ->addFormHeaderButton(function() {
                            return '<a href="/pilot/faqcategory" class="btn btn-info btn-sm">Back to FAQ Categories</a>';
                        })
                        ->addFormHeaderButton(function() {
                            return '<a href="/pilot/faq?view=published" class="btn btn-primary btn-sm">Back to FAQs</a>';
                        })
                        ->addFormFooterButton(function() {
                            return '<a href="/pilot/testing" class="mt-3 btn btn-danger btn" data-toggle="modal" data-target="#relationships-manager-modal">Delete</a>';
                        });



                        /************************************************************************************
                         *  Pilot plugin: FAQ Category index view                                          *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->applyScopes()
                        ->paginate(10);
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.sort_method') == 'manual_sort') {
                            $dynamo->addIndex('hamburger', 'Sort', function ($item) {
                                return '<i class="fas fa-bars fa-2x" ></i>';
                            });
                        }
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.name', true)) {
                            $dynamo->addIndex('name');
                        }
                        $dynamo->addIndex('id', 'Order FAQs in this Category',function ($item) {
                            return '<a href="' . route('admin.faqcategory.faqs', ['id' => $item->id]) . '" class="btn btn-success">Order</a>';
                        });
                        $dynamo->addIndex('numberOf', 'Number of FAQ\'s in this category', function($item) {
                            return $item->faqs()->withoutGlobalScope(PublishedScope::class)->count();
                        });

                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.status', true)) {
                            $dynamo->addIndex('test', 'Published?', function ($item) {
                                return $item->status == 30 ? '<i class="far fa-check-circle fa-3x" style="color: green; padding-top: 10px;"></i>' : '<i class="far fa-times-circle fa-3x" style="color: red; padding-top: 10px;"></i>';
                            });
                        }
                        $dynamo->addActionButton(function($item) {
                            return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                        })
                        ->addActionButton(function($item) {
                            return '<a href="faqcategory/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                        });
                        if (config('pilot.plugins.faqs.children.manage_faq_categories.fields.sort_method') == 'manual_sort') {
                            $dynamo->indexOrderBy('position');
                        } else {
                            $dynamo->indexOrderBy('name');
                        }
                        
                        $dynamo->ignoredScopes([PublishedScope::class]);

        return $dynamo;
    }

    /**
     * Copy the Project Category
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $faqCategory = FaqCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $newFaqCategory = $faqCategory->duplicate();

        // set success message
        \Session::flash('alert-success', 'Category copied successfully!');

        return redirect()->route('admin.faqcategory.edit', array($newFaqCategory->id));
    }

    /**
    * Remove the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        $category = FaqCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.faqcategory.index');
    }

    /**
    *  Reorder ProjectCategories
    *
    * @param  int  $id
    * @return Response
    */
    public function reorderFaqCategories()
    {
        $ids = request()->input('ids');

        foreach ($ids as $position => $catID) {
            $cat = FaqCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($catID);

            $cat->position = $position;

            $cat->save();
        }

        return $ids;
    }


    /**
     * Returns a view where admin can see and reorder faqs within this category
     *
     * @return View
     */
    public function faqs($id)
    {
        $faqcategory = FaqCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);
        
        $items = $faqcategory->faqs()->withoutGlobalScope(PublishedScope::class)->get();

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
        $faqcategory = FaqCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        $ids = request()->input('ids');

        foreach ($ids as $position => $faqID) {
            $faq = FaqFacade::withoutGlobalScope(PublishedScope::class)->find($faqID);
            $faq->faq_categories()->withoutGlobalScope(PublishedScope::class)->updateExistingPivot($faqcategory->id, compact('position'));
        }

        return $ids;
    }
}
