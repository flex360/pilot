<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Jzpeepz\Dynamo\IndexTab;
use Jzpeepz\Dynamo\FieldGroup as Group;
use Flex360\Pilot\Scopes\TestimonialsWithMediaScope;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\Testimonial as TestimonialFacade;

class TestimonialController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(TestimonialFacade::getFacadeRoot()));
                            // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                            if (config('pilot.plugins.testimonials.display_name') != null) {
                                $dynamo->alias(Str::singular(config('pilot.plugins.testimonials.display_name')));
                            }


                            /************************************************************************************
                             *  Pilot plugin: Testimonial form view                                              *
                             *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                             ************************************************************************************/
                            $dynamo->addIndexButton(function () {
                                return '<a href="/testimonials" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i> View Testimonials</a>';
                            });

                            //create custom info group
                            if (config('pilot.plugins.testimonials.fields.international_testimonials', false)) {
                                $customerInfoGroup = Group::make('customer_info', ['label' => '<b>Customer/Friend Info</b>','class' => 'col-md-12'])
                                                            ->rowStart();

                                                            if (config('pilot.plugins.testimonials.fields.name', false)) {
                                                                $customerInfoGroup->text('name');
                                                            }
                                                            if (config('pilot.plugins.testimonials.fields.city', false)) {
                                                                $customerInfoGroup->text('city');
                                                            }
                                                            if (config('pilot.plugins.testimonials.fields.state', false)) {
                                                                $customerInfoGroup->text('state');
                                                            }
                                                            if (config('pilot.plugins.testimonials.fields.country', false)) {
                                                                $customerInfoGroup->select('country', [
                                                                    'options' => getCountriesArray(),
                                                                ]);
                                                            }
                                                            $customerInfoGroup->rowEnd();
                            } else {
                                $customerInfoGroup = Group::make('customer_info', ['label' => '<b>Customer/Friend Info</b>','class' => 'col-md-12'])
                                                            ->rowStart();
                                                            if (config('pilot.plugins.testimonials.fields.name', false)) {
                                                                $customerInfoGroup->text('name');
                                                            }
                                                            if (config('pilot.plugins.testimonials.fields.city', false)) {
                                                                $customerInfoGroup->text('city');
                                                            }
                                                            if (config('pilot.plugins.testimonials.fields.state', false)) {
                                                                $customerInfoGroup->text('state');
                                                            }
                                                            $customerInfoGroup->rowEnd();
                            }

                            // attach the customerInfoGroup
                            $dynamo->group($customerInfoGroup);

                            // create Quote Group
                            $quoteGroup = Group::make('quote_group', ['label' => '<b>Testimonial</b>','class' => 'col-md-12 quote-group'])
                                                ->rowStart();

                                                if (config('pilot.plugins.testimonials.fields.quote', false)) {
                                                    $quoteGroup->textarea('quote', [
                                                        'class' => 'wysiwyg-editor',
                                                        'label' => 'Quote',
                                                        'help' => '<mark><strong>REQUIRED: </strong> If left blank, this testimonial will be filtered out of the frontend of the website.</mark>',
                                                    ]);
                                                }
                                                if (config('pilot.plugins.testimonials.fields.attribution', false)) {
                                                    $quoteGroup->text('attribution', [
                                                        'help' => 'Leave the attribution field blank to make this quote and testimonial anonymous.'
                                                    ]);
                                                }
                                                if (config('pilot.plugins.testimonials.fields.status', false)) {
                                                    $quoteGroup->select('status', [
                                                        'options' => TestimonialFacade::getStatuses(),
                                                        'help' => 'Use the "Draft" status to save information as you have it. When you\'re ready for a Testimonial to
                                                        show up on the front end of the website, change it to "Published" and then click the "Save Testimonial" button.',
                                                        'position' => 500,
                                                    ]);
                                                }
                                                $quoteGroup->rowEnd();

                            // attach the Quote Group
                            $dynamo->group($quoteGroup)
                                    ->removeField('deleted_at');

                            /************************************************************************************
                             *  Pilot plugin: Department index view                                              *
                             *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                             ************************************************************************************/
                            // ->applyScopes()
                            $dynamo->indexTab(
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
                            ->clearIndexes();

                            if (config('pilot.plugins.testimonials.fields.name', false)) {
                                $dynamo->addIndex('name');
                            }
                            if (config('pilot.plugins.testimonials.fields.quote', false)) {
                                $dynamo->addIndex('quote', 'Quote', function ($testimonial) {
                                    return $testimonial->getQuoteDisplayBackend();
                                });
                            }
                            if (config('pilot.plugins.testimonials.fields.attribution', false)) {
                                $dynamo->addIndex('attribution');
                            }
                            
                            $dynamo->addIndex('updated_at', 'Last Edited')
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
