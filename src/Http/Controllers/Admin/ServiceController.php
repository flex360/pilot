<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\FieldGroup;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\Service as ServiceFacade;
use Flex360\Pilot\Facades\ServiceCategory as ServiceCategoryFacade;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;
use Jzpeepz\Dynamo\IndexTab;

class ServiceController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(ServiceFacade::getFacadeRoot()));
                    // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.services.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.services.display_name')));
                    }


                    /************************************************************************************
                     *  Pilot plugin: Service form view                                                *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    if (config('pilot.plugins.services.fields.categories', true)) {
                        $dynamo->addIndexButton(function() {
                            return '<a href="/pilot/servicecategory" class="btn btn-primary btn-sm">Service Categories</a>';
                        });
                    }
                    
                    $dynamo->addIndexButton(function () {
                        return '<a href="'. route('service.index') . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View Services</a>';
                    });
                    if (config('pilot.plugins.services.fields.icon', true)) {
                        $dynamo->singleImage('icon', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 1000,
                        ]);
                    }
                    if (config('pilot.plugins.services.fields.title', true)) {
                        $dynamo->text('title');
                    }
                    if (config('pilot.plugins.services.fields.featured_image', true)) {
                        $dynamo->singleImage('featured_image', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 1000,
                        ]);
                    }
                    if (config('pilot.plugins.services.fields.subservices', true)) {
                        $dynamo->textarea('subservices', [
                            'help' => 'Include each subservice on a new line by clicking the "return" or "enter" key. For example, if this Service is titled “haircuts”, you might include subservices such as “Adult cuts", "Kids cuts", "Cut & Shampoo", and "Beard Trimming"'
                        ]);
                    }
                    if (config('pilot.plugins.services.fields.description', true)) {
                        $dynamo->textarea('description', [
                            'help' => 'Please write a description of what this service entails.'
                        ]);
                    }
                    if (config('pilot.plugins.services.fields.projects_selector', true)) {
                        $dynamo->hasManySimple('projects', [
                            'nameField' => 'title',
                            'modelClass' => ProjectFacade::class,
                            'label' => 'Projects',
                            'help' => 'Projects must already exist. If they don\'t, please save this service as a draft without assigned projects
                                          and go to the <a href="/pilot/project?view=published" target="_blank">Project Manager</a> to create the desired Project.',
                        ]);
                    }
                    if (config('pilot.plugins.services.fields.categories', true)) {
                        $dynamo->hasManySimple('service_categories', [
                            'nameField' => 'name',
                            'modelClass' => ServiceCategoryFacade::class,
                            'label' => 'Service Categories',
                            'help' => 'Categories must already exist. If they don\'t, please save this service as a draft without assigned categories
                                          and go to the <a href="/pilot/servicecategory?view=published" target="_blank">Service Category Manager</a> to create the desired category.',
                        ]);
                    }
                    if (config('pilot.plugins.services.fields.status', true)) {
                        $dynamo->select('status', [
                            'options' => ServiceFacade::getStatuses(),
                            'help' => 'Use the "Draft" status to save information as you have it. When you\'re ready for a Service to
                                          show up on the front end of the website, change it to "Published" and then click the "Save Service" button.',
                            'position' => 200,
                        ]);
                    }
                    $dynamo->removeField('deleted_at');

                    /************************************************************************************
                     *  Pilot plugin: Service index view                                               *
                     *  Check the plugins 'fields' array and set the index view for this module        *
                     ************************************************************************************/
                    $dynamo->clearIndexes()
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
                    ->searchable('title')
                    ->searchOptions([
                        'placeholder' => 'Search By Title',
                    ]);
                    if (config('pilot.plugins.services.fields.featured_image', true)) {
                        $dynamo->addIndex('featured_image', 'Featured Image', function ($item) {
                            if (empty($item->featured_image)) {
                                return '';
                            }
                            return '<img style="width: 100px  " src="' . $item->featured_image__thumb . '" class="" style="width: 60px;">';
                        });
                    }
                    if (config('pilot.plugins.services.fields.title', true)) {
                        $dynamo->addIndex('title');
                    }
                    if (config('pilot.plugins.services.fields.description', true)) {
                        $dynamo->addIndex('description', 'Description', function ($item) {
                            return $item->getDescriptionBackend();
                        });
                    }
                    $dynamo->addIndex('updated_at', 'Last Edited')
                    ->addActionButton(function($item) {
                        return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                    })
                    ->addActionButton(function($item) {
                        return '<a href="service/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="service/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')"  class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('title');

        return $dynamo;

    }

    /**
     * Copy the Service
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $service = ServiceFacade::find($id);

        $newService = $service->duplicate();

        // set success message
        \Session::flash('alert-success', 'Service copied successfully!');

        return redirect()->route('admin.service.edit', array($newService->id));
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $service = ServiceFacade::find($id);

        $service->delete();

        // set success message
        \Session::flash('alert-success', 'service deleted successfully!');

        return \Redirect::to('/pilot/service?view=published');
    }
}
