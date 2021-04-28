<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use App\Http\Requests;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Jzpeepz\Dynamo\FieldGroup;
use App\Http\Controllers\Controller;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\Service as ServiceFacade;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;
use Flex360\Pilot\Facades\ServiceCategory as ServiceCategoryFacade;

class ServiceCategoryController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(ServiceCategoryFacade::getFacadeRoot()));
                    // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.services.children.manage_service_categories.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.services.children.manage_service_categories.display_name')));
                    }

                    /***********************************************************************************
                     *  Pilot plugin: Service Category form view                                       *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    $dynamo->addFormHeaderButton(function() {
                        return '<a href="/pilot/servicecategory" class="btn btn-info btn-sm">Back to Service Categories</a>';
                    })
                    ->addFormHeaderButton(function() {
                        return '<a href="/pilot/service?view=published" class="btn btn-primary btn-sm">Back to Services</a>';
                    })
                    ->removeBoth('deleted_at');
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.name', true)) {
                        $dynamo->text('name', [
                            'class' => 'category-name-for-delete-modal',
                        ]);
                    }
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.featured_image', true)) {
                        $dynamo->singleImage('featured_image', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 1000,
                        ]);
                    }
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.service_selector', true)) {
                        $dynamo->hasMany('services', [
                            'options' => ServiceFacade::all()->pluck('title', 'id'),
                            'label' => 'Services',
                            'class' => 'category-dual-list',
                            'id' => 'category-dual-list',
                            'tooltip' => 'Select the Services you would like to belong to this category.',
                        ]);
                    }
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.status', true)) {
                        $dynamo->select('status', [
                            'options' => ServiceCategoryFacade::getStatuses(),
                            'help' => 'Use the \'\'Draft\'\' status to save information as you have it. When you\'re ready for a Service Category to
                                          show up on the front end of the website, change it to \'\'Published\'\' and then click the \'\'Save Service Category\'\' button.',
                            'position' => 200,
                        ]);
                    }
                    
                    /************************************************************************************
                     *  Pilot plugin: Service Category index view                                      *
                     *  Check the plugins 'fields' array and set the index view for this module        *
                     ************************************************************************************/
                    $dynamo->addIndexButton(function() {
                        return '<a href="/pilot/service?view=published" class="btn btn-primary btn-sm">Back to Services</a>';
                    })
                    ->applyScopes();

                    if (config('pilot.plugins.services.children.manage_service_categories.fields.sort_method') == 'manual_sort') {
                        $dynamo->addIndex('hamburger', 'Sort', function ($item) {
                            return '<i class="fas fa-bars fa-2x" ></i>';
                        });
                    }
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.name', true)) {
                        $dynamo->addIndex('name');
                    }
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.service_sort_method') == 'manual_sort') {
                        $dynamo->addIndex('id', 'Order Services in this Category',function ($item) {
                            return '<a href="' . route('admin.servicecategory.services', ['id' => $item->id]) . '" class="btn btn-success">Order</a>';
                        });
                    }

                    $dynamo->addIndex('count', 'Number of Services\'s in this category', function($item) {
                        return $item->services->count();
                    });
                    if (config('pilot.plugins.services.children.manage_service_categories.fields.status', true)) {
                        $dynamo->addIndex('test', 'Published?', function ($item) {
                            return $item->status == 30 ? '<i class="far fa-check-circle fa-3x" style="color: green; padding-top: 10px;"></i>' : '<i class="far fa-times-circle fa-3x" style="color: red; padding-top: 10px;"></i>';
                        });
                    }
                    $dynamo->addIndex('updated_at', 'Last Edited');
                    
                    $dynamo->addActionButton(function($item) {
                        return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                    })
                    ->addActionButton(function($item) {
                        return '<a href="servicecategory/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->hideDelete()
                    ->addFormFooterButton(function() {
                        return '<a href="/pilot/testing" class="mt-3 btn btn-danger btn" data-toggle="modal" data-target="#relationships-manager-modal">Delete</a>';
                    });

                    if (config('pilot.plugins.services.children.manage_service_categories.fields.sort_method') == 'manual_sort') {
                        $dynamo->indexOrderBy('position');
                    } else {
                        $dynamo->indexOrderBy('name');
                    }
                    

        return $dynamo;
    }


    /**
     * Copy the Service Category
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $serviceCategory = ServiceCategoryFacade::find($id);

        $newServiceCategory = $serviceCategory->duplicate();

        // set success message
        \Session::flash('alert-success', 'Category copied successfully!');

        return redirect()->route('admin.servicecategory.edit', array($newServiceCategory->id));
    }

    /**
    * Remove the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        $category = ServiceCategoryFacade::find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.servicecategory.index');
    }

    /**
    *  Reorder ServiceCategories
    *
    * @param  int  $id
    * @return Response
    */
    public function reorderServiceCategories()
    {
        $ids = request()->input('ids');

        foreach ($ids as $position => $catID) {
            $cat = ServiceCategoryFacade::find($catID);

            $cat->position = $position;

            $cat->save();
        }

        return $ids;
    }


    /**
     * Returns a view where admin can see and reorder services within this category
     *
     * @return View
     */
    public function services($id)
    {
        $serviceCategory = ServiceCategoryFacade::find($id);
        
        $items = $serviceCategory->services()->orderBy(config('pilot.table_prefix') . 'service_' . config('pilot.table_prefix') . 'service_category.position')->get();

        $dynamo = (new ServiceController)->getDynamo();

        return view('pilot::admin.dynamo.services.reorder', compact('dynamo', 'items', 'serviceCategory'));
    }

    /**
     * Functions runs on 'reorder' of Services within this category
     *
     * @return View
     */
    public function reorderServicesWithinCategory($id)
    {
        $serviceCategory = ServiceCategoryFacade::find($id);

        $ids = request()->input('ids');

        foreach ($ids as $position => $serviceID) {
            $service = ServiceFacade::find($serviceID);
            $service->service_categories()->updateExistingPivot($serviceCategory->id, compact('position'));
        }

        return $ids;
    }
}
