<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Facades\Testimonial as TestimonialFacade;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Jzpeepz\Dynamo\IndexTab;
use Jzpeepz\Dynamo\FieldGroup as Group;
use Flex360\Pilot\Scopes\TestimonialsWithMediaScope;

class TestimonialController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(TestimonialFacade::getFacadeRoot()))
            
            ->addIndexButton(function () {
                return '<a href="/testimonials" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i> View Testimonials</a>';
            });

        if (config('pilot.plugins.testimonials.international-testimonials', false)) {
            $customerInfoGroup = Group::make('customer_info', ['label' => '<b>Customer/Friend Info</b>','class' => 'col-md-12'])
                                        ->rowStart()
                                        ->text('name')
                                        ->text('city')
                                        ->text('state')
                                        ->select('country', [
                                            'options' => getCountriesArray(),
                                        ])
                                        ->rowEnd();
        } else {
            $customerInfoGroup = Group::make('customer_info', ['label' => '<b>Customer/Friend Info</b>','class' => 'col-md-12'])
                                        ->rowStart()
                                        ->text('name')
                                        ->text('city')
                                        ->select('country', [
                                            'options' => getStatesArray(),
                                        ])
                                        ->rowEnd();
        }
            //Customer Info Group
            $dynamo->group($customerInfoGroup) 
            //end of Customer Info Group

            //Quote Group
            ->group(
                Group::make(
                    'quote_group',
                    [
                        'label' => '<b>Testimonial</b>',
                        'class' => 'col-md-12 quote-group'
                    ]
                )
                    ->rowStart()
                    ->textarea('quote', [
                        'class' => 'wysiwyg-editor',
                        'label' => 'Quote',
                        'help' => '<mark><strong>REQUIRED: </strong> If left blank, this testimonial will be filtered out of the frontend of the website.</mark>',
                    ])
                    ->text('attribution', [
                        'help' => 'Leave the attribution field blank to make this quote and testimonial anonymous.'
                    ])
                    ->rowEnd()
            ) //end of Quote Group


            ->select('status', [
                'options' => TestimonialFacade::getStatuses(),
                'help' => 'Save a draft to come back to this later. Published testimonials will be automatically displayed on the front-end of the website after you save.',
                'position' => 500,
            ])
            ->removeField('deleted_at')

            //set index view
            // ->applyScopes()
            ->indexTab(
                IndexTab::make('Published', function ($query) {
                    return $query->where('status', 30)->orderBy('name');
                })
                    ->setBadgeColor('blue') // default is red if you don't supply
                    ->showCount()
            )
            ->indexTab(
                IndexTab::make('Drafts', function ($query) {
                    return $query->where('status', 10);
                })
                    ->showCount()
            )
            ->paginate(25)
            ->searchable('name')
            ->searchOptions([
                'placeholder' => 'Search By Name',
            ])
            ->clearIndexes()
            ->addIndex('name')
            ->addIndex('quote', 'Quote', function ($testimonial) {
                return $testimonial->getQuoteDisplayBackend();
            })
            ->addIndex('attribution')
            ->addIndex('updated_at', 'Last Edited')
            ->addActionButton(function ($item) {
                return '<a href="testimonial/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
            })
            ->addActionButton(function ($item) {
                return '<a href="testimonial/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')"  class="btn btn-secondary btn-sm">Delete</a>';
            })
            ->indexOrderBy('name');

            return $dynamo;
    }

    /**
     * Copy the Testimonial
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $testimonial = TestimonialFacade::find($id);

        $newTestimonial = $testimonial->duplicate();

        // set success message
        \Session::flash('alert-success', 'Testimonial copied successfully!');

        return redirect()->route('admin.testimonial.edit', [$newTestimonial->id]);
    }

    /**
     * Remove the specified testimonial from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $testimonial = TestimonialFacade::find($id);

        $testimonial->delete();

        // set success message
        \Session::flash('alert-success', 'Testimonial deleted successfully!');

        return \Redirect::to('/pilot/testimonial?view=published');
    }
}
