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
use Jzpeepz\Dynamo\IndexTab;

class FaqController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(FaqFacade::getFacadeRoot()))
                    ->auto()
                    ->addIndexButton(function() {
                        return '<a href="/pilot/faqcategory" class="btn btn-primary btn-sm">FAQ Categories</a>';
                    })
                    ->addIndexButton(function () {
                        return '<a href="'. route('faq.index') . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View FAQs</a>';
                    });

                    /**
                     *  Pilot config: FAQ uses long/short answer
                     *  Check config file to see if we use short/long answer. If long, use WYSIWYG, if short, use normal text field
                     * 
                     */ 
                    if (config('pilot.plugins.faqs.uses-long-answers', false)) {
                        $dynamo->textarea('answer', [
                            'class' => 'wysiwyg-editor',
                            'help' => 'Please use this space to provide a full / detailed answer to the question, including any stipulations,
                                            restrictions, and other related & necessary details.'
                        ]);
                    } else {
                        $dynamo->text('answer', [
                            'help' => 'Maximum character count is 255. The input field will stop taking input at that point.',
                            'class' => 'character-limited',
                            'maxlength' => 255,
                        ]);
                    } 
                    
                    $dynamo->hasManySimple('faq_categories', [
                        'nameField' => 'question',
                        'modelClass' => FaqFacade::class,
                        'label' => 'FAQ Categories',
                        'help' => 'Categories must already exist. If they don\'t, please save a draft without assigned categories
                                      and go to the category manager to create the desired category.',
                        'position' => 40,
                    ])
                    ->select('status', [
                        'options' => FaqFacade::getStatuses(),
                        'help' => 'Use the \'\'Draft\'\' status to save information as you have it. When you\'re ready for an FAQ to
                                      show up on the front end of the website, change it to \'\'Published\'\' and then click the \'\'Save FAQ\'\' button.',
                        'position' => 200,
                    ])
                    ->setFormPanelTitle("FAQ")
                    ->setSaveItemText('Save FAQ')
                    ->removeField('deleted_at')

                    //set index view
                    ->setIndexPanelTitle("FAQ Manager")
                    ->setAddItemText('Add FAQ')
                    ->applyScopes()
                    ->paginate(25)
                    ->indexTab(IndexTab::make('Published', function ($query) {
                            return $query->where('status', 30)->whereNull('deleted_at');
                        })
                        ->setBadgeColor('blue') // default is red if you don't supply
                        ->showCount()
                    )

                    ->indexTab(IndexTab::make('Drafts', function ($query) {
                            return $query->where('status', 10)->whereNull('deleted_at');
                        })
                        ->showCount()
                    )
                    ->searchable('question')
                    ->searchOptions([
                        'placeholder' => 'Search By Question',
                    ])
                    ->clearIndexes()
                    ->addIndex('question')
                    ->addIndex('answer', 'Answer', function ($item) {
                        return $item->getShortAnswerBackend();
                    })
                    ->addIndex('category', 'Applicable Categories', function ($item) {
                        return $item->faq_categories->transform(function($cat) {
                          return $cat->name;

                        })
                        ->implode (', ');

                    })
                    ->addIndex('updated_at', 'Last Edited')
                    // ->addActionButton(function($item) {
                    //     return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                    // })
                    ->addActionButton(function($item) {
                        return '<a href="faq/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="faq/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')"  class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('question');

        return $dynamo;

    }

        /**
         * Copy the Faq
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function copy($id)
        {
            $faq = FaqFacade::find($id);

            $newFaq = $faq->duplicate();

            // set success message
            \Session::flash('alert-success', 'Faq copied successfully!');

            return redirect()->route('admin.faq.edit', array($newFaq->id));
        }

        /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function destroy($id)
        {
            $faq = FaqFacade::find($id);

            $faq->delete();

            // set success message
            \Session::flash('alert-success', 'Faq deleted successfully!');

            return \Redirect::to('/pilot/faq?view=published');
        }
}
