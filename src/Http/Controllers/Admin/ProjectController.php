<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use App\Http\Requests;
use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Jzpeepz\Dynamo\IndexTab;
use Jzpeepz\Dynamo\FieldGroup;
use App\Http\Controllers\Controller;
use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\Service as ServiceFacade;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;

class ProjectController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(ProjectFacade::getFacadeRoot()));
                    // check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.projects.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.projects.display_name')));
                    }


                    /************************************************************************************
                     *  Pilot plugin: Project form view                                                *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    if (config('pilot.plugins.projects.fields.categories', true)) {
                        $dynamo->addIndexButton(function() {
                            return '<a href="/pilot/projectcategory" class="btn btn-primary btn-sm">Project Categories</a>';
                        });
                    }
                    $dynamo->addIndexButton(function () {
                        return '<a href="'. route('project.index') . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View Projects</a>';
                    });
                    if (config('pilot.plugins.projects.fields.title', true)) {
                        $dynamo->text('title');
                    }
                    if (config('pilot.plugins.projects.fields.featured_image', true)) {
                        $dynamo->singleImage('featured_image', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 2000,
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.image_background_swatch_picker', true)) {
                        $dynamo->select('fi_background_color', [], function ($item, $field) {
                            return view('pilot::admin.dynamo.projects._fi_background_color_picker', compact('item', 'field'))->render();
                        });
                    }
                    if (config('pilot.plugins.projects.fields.summary', true)) {
                        $dynamo->textarea('summary', [
                            'help' => 'Please include 2-3 short sentences about the project.'
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.location', true)) {
                        $dynamo->text('location', [
                            'help' => 'Please include the location of this project in the following format: City name, state. Example:  Little Rock, AR'
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.completion_date', true)) {
                        $dynamo->text('completion_date', [
                            'class' => 'datepicker',
                            'help' => 'Please use the Date Selector to select month, day, and year at which this project was completed.'
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.gallery', true)) {
                        $dynamo->gallery('gallery', [
                            'label' => 'Gallery',
                            'help' => 'Use uploader or browse option to select multiple images. Re-order images by clicking on them, dragging, and dropping to the desired order',
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.categories', true)) {
                        $dynamo->hasManySimple('project_categories', [
                            'nameField' => 'name',
                            'modelClass' => ProjectCategoryFacade::class,
                            'options' => ProjectCategoryFacade::withoutGlobalScope(PublishedScope::class)->orderBy('name')->pluck('name', 'id'),
                            'value' => function ($item, $field) {
                                return $item->{$field->key}()->withoutGlobalScopes()->pluck('id')->toArray();
                            },
                            'label' => 'Project Categories',
                            'help' => 'Categories must already exist. If they don\'t, please save a draft without assigned categories
                                          and go to the <a href="/pilot/projectcategory" target="_blank">Project Category Manager</a> to create the desired category.',
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.services', true)) {
                        $dynamo->hasManySimple('services', [
                            'nameField' => 'title',
                            'modelClass' => ServiceFacade::class,
                            'options' => ServiceFacade::withoutGlobalScope(PublishedScope::class)->orderBy('title')->pluck('title', 'id'),
                            'value' => function ($item, $field) {
                                return $item->{$field->key}()->withoutGlobalScopes()->pluck('id')->toArray();
                            },
                            'label' => 'Services',
                            'help' => 'Services must already exist. If they don\'t, please save this project as a draft without assigned services
                                          and go to the <a href="/pilot/service?view=published" target="_blank">Service Manager</a> to create the desired service.',
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.featured_project', true)) {
                        $dynamo->checkbox('featured', [
                            'label' => 'Featured Project',
                            'help' => 'Check this box if you want this project to be featured. It will show up first in the Project List view on the frontend of the website and any other areas of the frontend of the website designed to show featured projects.',
                        ]);
                    }
                    if (config('pilot.plugins.projects.fields.status', true)) {
                        $dynamo->select('status', [
                            'options' => ProjectFacade::getStatuses(),
                            'help' => 'Use the "Draft" status to save information as you have it. When you\'re ready for a Project to
                                          show up on the front end of the website, change it to "Published" and then click the "Save Project" button.',
                            'position' => 200,
                        ]);
                    }
                    $dynamo->removeField('deleted_at');

                    /************************************************************************************
                     *  Pilot plugin: Project index view                                               *
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
                    if (config('pilot.plugins.projects.fields.featured_image', true)) {
                        $dynamo->addIndex('featured_image', 'Featured Image', function ($item) {
                            if (empty($item->featured_image)) {
                                return '';
                            }
                            return '<img style="width: 100px  " src="' . $item->featured_image__thumb . '" class="" style="width: 60px;">';
                        });
                    }
                    if (config('pilot.plugins.projects.fields.title', true)) {
                        $dynamo->addIndex('title');
                    }
                    if (config('pilot.plugins.projects.fields.summary', true)) {
                        $dynamo->addIndex('summary', 'Summary', function ($item) {
                            return $item->getSummaryBackend();
                        });
                    }
                    if (config('pilot.plugins.projects.fields.categories', true)) {
                        $dynamo->addIndex('category', 'Applicable Categories', function ($item) {
                            return $item->project_categories->transform(function($cat) {
                              return $cat->title;
                            })
                            ->implode (', ');
    
                        });
                    }
                    if (config('pilot.plugins.projects.fields.services', true)) {
                        $dynamo->addIndex('category', 'Applicable Services', function ($item) {
                            return $item->services->transform(function($cat) {
                              return $cat->title;
                            })
                            ->implode (', ');
    
                        });
                    }
                    $dynamo->addIndex('updated_at', 'Last Edited')
                    ->addActionButton(function($item) {
                        return '<a href="'.$item->url().'" target="_blank"  class="btn btn-secondary btn-sm">View</a>';
                    })
                    ->addActionButton(function($item) {
                        return '<a href="project/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="project/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')"  class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->ignoredScopes([PublishedScope::class])
                    ->indexOrderBy('title');

        return $dynamo;

    }

        /**
         * Copy the Project
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function copy($id)
        {
            $project = ProjectFacade::withoutGlobalScope(PublishedScope::class)->find($id);

            $newProject = $project->duplicate();

            // set success message
            \Session::flash('alert-success', 'Project copied successfully!');

            return redirect()->route('admin.project.edit', array($newProject->id));
        }

        /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function destroy($id)
        {
            $project = ProjectFacade::withoutGlobalScope(PublishedScope::class)->find($id);

            $project->delete();

            // set success message
            \Session::flash('alert-success', 'Project deleted successfully!');

            return \Redirect::to('/pilot/project?view=published');
        }
}
