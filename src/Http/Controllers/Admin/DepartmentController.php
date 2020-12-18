<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Facades\Department as DepartmentFacade;
use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\FormTab;
use Flex360\Pilot\Pilot\Tag;
use Illuminate\Http\Request;
use Flex360\Pilot\Pilot\Employee;
use Flex360\Pilot\Pilot\Department;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Pilot\Resource;

class DepartmentController extends DynamoController
{
    public function getDynamo()
    {
        $departmentDeatilsFormTab = FormTab::make('Department Details')
        ->text('name')
        ->textarea('intro_text', [
            'class' => 'wysiwyg-editor',
        ])
        ->singleImage('featured_image', [
            'maxWidth' => 1000,
            'label' => 'Featured Image',
            'help' => 'Upload featured image. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
        ])
        ->hasMany('employees', [
            'options' => Employee::orderBy('first_name')->get()->pluck('fullName', 'id'),
            'class' => 'category-dual-list',
            'id' => 'category-dual-list',
            'help' => 'Select the Employees/People that are a member of this department.',
        ]);

        // if departments has tags_relationship enabled, include HasManySimple multi-selector
        if (config('pilot.plugins.employees.children.departments.tags_relationship')) {
            // $departmentDeatilsFormTab->hasManySimple('tags', [
            //     'modelClass' => 'Flex360\Pilot\Pilot\Tag',
            //     'help' => 'Select the relevant Tags for this Department. Tags let this Department become connected and display the most recentt News post and Events that have that
            //                same Tag.<br> Example: If a news post is tagged <strong>\'\'Employee Benefits\'\'</strong>, and this Department is also tagged <strong>\'\'Employee Benefits\'\'</strong>,
            //                then we can display <strong>\'\'Employee Benefits\'\'</strong> News on this department page.'
            // ]);
            $departmentDeatilsFormTab->hasMany('tags', [
                'modelClass' => 'Flex360\Pilot\Pilot\Tag',
                'options' => Tag::orderBy('name')->get()->pluck('name', 'id'),
                'class' => 'category-dual-list',
                'id' => 'category-dual-list',
                'help' => 'Select the relevant Tags for this Department.<br> Example: If you select the tag <strong>\'\'Cute Puppies\'\'</strong>,
                then <strong>\'\'Cute Puppies\'\'</strong> News will display on this department page.',
            ]);
        }
        // if departments has resources_relationship enabled, include HasManySimple multi-selector
        if (config('pilot.plugins.employees.children.departments.resources_relationship')) {
            // $departmentDeatilsFormTab->hasManySimple('resources', [
            //     'modelClass' => 'Flex360\Pilot\Pilot\Resource',
            //     'nameField' => 'title',
            //     'help' => 'Select the resources that this department needs access to on a regular basis. Resources will be listed in alphabetical order on the department page. You must <a href="/pilot/resource" target="_blank">create & publish Resources</a> before they will be available here.'
            // ]);
            $departmentDeatilsFormTab->hasMany('resources', [
                'modelClass' => 'Flex360\Pilot\Pilot\Resource',
                'options' => Resource::orderBy('title')->get()->pluck('title', 'id'),
                'class' => 'category-dual-list',
                'id' => 'category-dual-list',
                'help' => 'Select the resources that this department needs access to on a regular basis.<br> Resources will 
                            be listed in alphabetical order on the department page. You must <a href="/pilot/resource" target="_blank">
                            create & publish Resources</a> before they will be available here.',
            ]);
        }

        $dynamo = Dynamo::make(get_class(DepartmentFacade::getFacadeRoot()))
                    ->auto()
                    ->text('name')
                    ->removeField('position')
                    ->removeField('deleted_at')
                    // main form tab
                    ->formTab($departmentDeatilsFormTab)
                    ->formTab(
                        FormTab::make('Metadata')
                            ->text('slug', [
                                'help' => 'Customize the link for this department page. For example "Human Resources" could be "/human_resources" or just "/hr".'
                            ])
                            ->textarea('summary')
                    ) // end of Upcoming Event Details FormTab

                      //clear indexes
                    ->clearIndexes()
                    // setup the index view

                    ->addIndex('hamburger', 'Sort', function ($item) {
                        return '<i class="fas fa-bars fa-2x" ></i>';
                    });

        // if departments has resources_relationship enabled, include HasManySimple multi-selector
        if (config('pilot.plugins.employees.children.departments.sort_employees_within_department')) {
            $dynamo->addIndex('id', 'Order Staff', function ($item) {
                //creates order staff button
                //return '<a href="' . route('staffers.reorder') . '">Order</a>'
                return '<a href="' . route('admin.department.staff', ['department' => $item->id]) . '" class="btn btn-success">Order</a>';
            });
        }

        $dynamo->addIndex('name')
                    ->addIndex('test', 'Published?', function ($item) {
                        return $item->status == 30 ? '<i class="far fa-check-circle fa-3x" style="color: green; padding-top: 10px;"></i>' : '<i class="far fa-times-circle fa-3x" style="color: red; padding-top: 10px;"></i>';
                    })
                    ->addIndex('count', 'Number Of Employees In This Department', function ($item) {
                        return $item->employees->count();
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="department/' . $item->id . '/copy" class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="department/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever. ( FLEX360 can bring it back for you )\')" class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('position');

        return $dynamo;
    }

    /**
    * Copy the Department
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function copy($id)
    {
        $department = DepartmentFacade::find($id);

        $newDepartment = $department->duplicate();

        // set success message
        \Session::flash('alert-success', 'Department copied successfully!');

        return redirect()->route('admin.department.edit', [$newDepartment->id]);
    }

    public function staffMembers($id)
    {
        $department = DepartmentFacade::find($id);
        //runs on 'pilot/department/{id}/staffMembers'
        //list all staff members in Department{id}
        $items = $department->employees()->orderBy(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee.position')->get();
        $dynamo = (new EmployeeController)->getDynamo();

        return view('pilot::admin.dynamo.employees.reorder', compact('dynamo', 'items', 'department'));
    }

    public function reorderDepartments()
    {
        //runs on 'pilot/department/{id}/staffMembers/reorder'
        //runs when user changes the order via drag and drop of the staff members in the departments
        //updates the position of that staff member within this department.

        //$departmentID = $id;
        $ids = request()->input('ids');

        foreach ($ids as $position => $departmentID) {
            $department = DepartmentFacade::find($departmentID);

            $department->position = $position;

            $department->save();
        }

        return $ids;
    }

    public function reorderStaffWithinDepartment($id)
    {
        //runs on 'pilot/department/{id}/staffMembers/reorderStaffWithDepartments'
        //runs when user changes the order via drag and drop of the staff members in the departments
        //updates the position of that staff member within this department.

        $department = DepartmentFacade::find($id);

        //$staffMemberID = $id;
        $ids = request()->input('ids');

        foreach ($ids as $position => $staffMemberID) {
            $staffMember = EmployeeFacade::find($staffMemberID);

            //$staffMember->position = $position;
            $staffMember->departments()->updateExistingPivot($department->id, compact('position'));
            //$staffMember->save();
        }

        return $ids;
    }
}
