<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\FieldGroup;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;

class ProjectCategoryController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(ProjectCategoryFacade::getFacadeRoot()));
                    // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.projects.children.manage_project_categories.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.projects.children.manage_project_categories.display_name')));
                    }

                    /***********************************************************************************
                     *  Pilot plugin: Project Category form view                                       *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    $dynamo->addFormHeaderButton(function() {
                        return '<a href="/pilot/projectcategory" class="btn btn-info btn-sm">Back to Project Categories</a>';
                    })
                    ->addFormHeaderButton(function() {
                        return '<a href="/pilot/project?view=published" class="btn btn-primary btn-sm">Back to Projects</a>';
                    })
                    ->removeBoth('deleted_at');
                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.name', true)) {
                        $dynamo->text('name', [
                            'class' => 'category-name-for-delete-modal',
                        ]);
                    }
                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.featured_image', true)) {
                        $dynamo->singleImage('featured_image', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 1000,
                        ]);
                    }
                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.project_selector', true)) {
                        $dynamo->hasMany('projects', [
                            'options' => ProjectFacade::all()->pluck('title', 'id'),
                            'label' => 'Projects',
                            'class' => 'category-dual-list',
                            'id' => 'category-dual-list',
                            'tooltip' => 'Select the Projects you would like to belong to this category.',
                        ]);
                    }
                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.status', true)) {
                        $dynamo->select('status', [
                            'options' => ProjectCategoryFacade::getStatuses(),
                            'help' => 'Use the \'\'Draft\'\' status to save information as you have it. When you\'re ready for a Project Category to
                                          show up on the front end of the website, change it to \'\'Published\'\' and then click the \'\'Save Project Category\'\' button.',
                            'position' => 200,
                        ]);
                    }
                    
                    /************************************************************************************
                     *  Pilot plugin: Project Category index view                                      *
                     *  Check the plugins 'fields' array and set the index view for this module        *
                     ************************************************************************************/
                    $dynamo->addIndexButton(function() {
                        return '<a href="/pilot/project?view=published" class="btn btn-primary btn-sm">Back to Projects</a>';
                    })
                    ->applyScopes();

                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.sort_method') == 'manual_sort') {
                        $dynamo->addIndex('hamburger', 'Sort', function ($item) {
                            return '<i class="fas fa-bars fa-2x" ></i>';
                        });
                    }

                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.featured_image', true)) {
                        $dynamo->addIndex('featured_image', 'Featured Image', function ($item) {
                            if (empty($item->featured_image__thumb)) {
                                return '';
                            }
                            return '<img style="width: 100px  " src="' . $item->featured_image__thumb . '" class="" style="width: 60px;">';
                        });
                    }
                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.name', true)) {
                        $dynamo->addIndex('name');
                    }
                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.project_sort_method') == 'manual_sort') {
                        $dynamo->addIndex('id', 'Order Projects in this Category',function ($item) {
                            return '<a href="' . route('admin.projectcategory.projects', ['id' => $item->id]) . '" class="btn btn-success">Order</a>';
                        });
                    }

                    $dynamo->addIndex('count', 'Number of Project\'s in this category', function($item) {
                        return $item->projects->count();
                    });

                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.status', true)) {
                        $dynamo->addIndex('test', 'Published?', function ($item) {
                            return $item->status == 30 ? '<i class="far fa-check-circle fa-3x" style="color: green; padding-top: 10px;"></i>' : '<i class="far fa-times-circle fa-3x" style="color: red; padding-top: 10px;"></i>';
                        });
                    }

                    $dynamo->addIndex('updated_at', 'Last Edited');
                    
                    $dynamo->addActionButton(function($item) {
                        return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                    })
                    ->addActionButton(function($item) {
                        return '<a href="projectcategory/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->hideDelete()
                    ->addFormFooterButton(function() {
                        return '<a href="/pilot/testing" class="mt-3 btn btn-danger btn" data-toggle="modal" data-target="#relationships-manager-modal">Delete</a>';
                    });

                    if (config('pilot.plugins.projects.children.manage_project_categories.fields.sort_method') == 'manual_sort') {
                        $dynamo->indexOrderBy('position');
                    } else {
                        $dynamo->indexOrderBy('name');
                    }

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
        $projectCategory = ProjectCategoryFacade::find($id);

        $newProjectCategory = $projectCategory->duplicate();

        // set success message
        \Session::flash('alert-success', 'Category copied successfully!');

        return redirect()->route('admin.projectcategory.edit', array($newProjectCategory->id));
    }

    /**
    * Remove the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function destroy($id)
    {
        $category = ProjectCategoryFacade::find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.projectcategory.index');
    }


    /**
    *  Reorder ProjectCategories
    *
    * @param  int  $id
    * @return Response
    */
    public function reorderProjectCategories()
    {
        $ids = request()->input('ids');

        foreach ($ids as $position => $catID) {
            $cat = ProjectCategoryFacade::find($catID);

            $cat->position = $position;

            $cat->save();
        }

        return $ids;
    }


    /**
     * Returns a view where admin can see and reorder projects within this category
     *
     * @return View
     */
    public function projects($id)
    {
        $projectCategory = ProjectCategoryFacade::find($id);
        
        $items = $projectCategory->projects()->orderBy(config('pilot.table_prefix') . 'project_' . config('pilot.table_prefix') . 'project_category.position')->get();

        $dynamo = (new ProjectController)->getDynamo();

        return view('pilot::admin.dynamo.projects.reorder', compact('dynamo', 'items', 'projectCategory'));
    }

    /**
     * Functions runs on 'reorder' of Projects within this category
     *
     * @return View
     */
    public function reorderProjectsWithinCategory($id)
    {
        $projectCategory = ProjectCategoryFacade::find($id);

        $ids = request()->input('ids');

        foreach ($ids as $position => $projectID) {
            $project = ProjectFacade::find($projectID);
            $project->project_categories()->updateExistingPivot($projectCategory->id, compact('position'));
        }

        return $ids;
    }
}
