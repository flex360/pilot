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
                        // department details form tab
                        $departmentDeatilsFormTab = FormTab::make('Department Details');
                        if (config('pilot.plugins.employees.children.departments.fields.name', true)) {
                            $departmentDeatilsFormTab->text('name');
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.intro_text', true)) {
                            $departmentDeatilsFormTab->textarea('intro_text', [
                                'class' => 'wysiwyg-editor',
                            ]);
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.featured_image', true)) {
                            $departmentDeatilsFormTab->singleImage('featured_image', [
                                'maxWidth' => 1000,
                                'label' => 'Featured Image',
                                'help' => 'Upload featured image. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            ]);
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.employees_selector', true)) {
                            $departmentDeatilsFormTab->hasMany('employees', [
                                'options' => Employee::orderBy('first_name')->get()->pluck('fullName', 'id'),
                                'class' => 'category-dual-list',
                                'id' => 'category-dual-list',
                                'help' => 'Select the Employees/People that are a member of this department.',
                            ]);
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.tags_relationship', true)) {
                            $departmentDeatilsFormTab->hasMany('tags', [
                                'modelClass' => 'Flex360\Pilot\Pilot\Tag',
                                'options' => Tag::orderBy('name')->get()->pluck('name', 'id'),
                                'class' => 'category-dual-list',
                                'id' => 'category-dual-list',
                                'help' => 'Select the relevant Tags for this Department.<br> Example: If you select the tag <strong>"Cute Puppies"</strong>,
                                then <strong>"Cute Puppies"</strong> News will display on this department page.',
                            ]);
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.resources_relationship', true)) {
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
                        if (config('pilot.plugins.employees.children.departments.fields.status', true)) {
                            $departmentDeatilsFormTab->select('status', [
                                'options' => DepartmentFacade::getStatuses(),
                                'help' => 'Use the "Draft" status to save information as you have it. When you\'re ready for a Department to
                                            show up on the front end of the website, change it to "Published" and then click the "Save Department" button.',
                                'position' => 10,
                            ]);
                        }

                        // metadata form tab
                        if (config('pilot.plugins.employees.children.departments.fields.slug', true) || config('pilot.plugins.employees.children.departments.fields.summary', true)) {
                            $metadataFormTab = FormTab::make('Metadata');
                            if (config('pilot.plugins.employees.children.departments.fields.slug', true)) {
                                $metadataFormTab->text('slug', [
                                    'help' => 'Customize the link for this department page. For example "Human Resources" could be "/human_resources" or just "/hr".'
                                ]);
                            }
                            if (config('pilot.plugins.employees.children.departments.fields.summary', true)) {
                                $metadataFormTab->textarea('summary');
                            }
                        }
                    
        $dynamo = Dynamo::make(get_class(DepartmentFacade::getFacadeRoot()));
                        //check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                        if (config('pilot.plugins.employees.children.departments.display_name') != null) {
                            $dynamo->alias(Str::singular(config('pilot.plugins.employees.children.departments.display_name')));
                        }



                        /************************************************************************************
                         *  Pilot plugin: Department form view                                              *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->removeField('position')
                        ->removeField('deleted_at');
                        $dynamo->addFormHeaderButton(function() {
                            return '<a href="/pilot/department" class="btn btn-info btn-sm">Back to Departments</a>';
                        })
                        ->addFormHeaderButton(function() {
                            return '<a href="/pilot/employee?view=published" class="btn btn-primary btn-sm">Back to Employees</a>';
                        });
                        $dynamo->formTab($departmentDeatilsFormTab);
                        if (isset($metadataFormTab)) {
                            $dynamo ->formTab($metadataFormTab);
                        }




                        /************************************************************************************
                         *  Pilot plugin: Department index view                                              *
                         *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                         ************************************************************************************/
                        $dynamo->clearIndexes();
                        $dynamo->addIndexButton(function() {
                            return '<a href="/pilot/employee?view=published" class="btn btn-primary btn-sm">Back to Employees</a>';
                        });
                        if (config('pilot.plugins.employees.children.departments.fields.sort_method') == 'manual_sort') {
                            $dynamo->addIndex('hamburger', 'Sort', function ($item) {
                                return '<i class="fas fa-bars fa-2x" ></i>';
                            });
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.name', true)) {
                            $dynamo->addIndex('name');
                        }
                        if (config('pilot.plugins.employees.children.departments.fields.sort_employees_within_department', true)) {
                            $dynamo->addIndex('id', 'Order Staff', function ($item) {
                                return '<a href="' . route('admin.department.staff', ['id' => $item->id]) . '" class="btn btn-success">Order</a>';
                            });
                        }
                        $dynamo->addIndex('count', 'Number Of Employees In This Department', function ($item) {
                            return $item->employees->count();
                        });
                        if (config('pilot.plugins.employees.children.departments.fields.status', true)) {
                            $dynamo->addIndex('test', 'Published?', function ($item) {
                                return $item->status == 30 ? '<i class="far fa-check-circle fa-3x" style="color: green; padding-top: 10px;"></i>' : '<i class="far fa-times-circle fa-3x" style="color: red; padding-top: 10px;"></i>';
                            });
                        }

                        $dynamo->addIndex('updated_at', 'Last Edited')

                        ->addActionButton(function ($item) {
                            return '<a href="department/' . $item->id . '/copy" class="btn btn-secondary btn-sm">Copy</a>';
                        })
                        ->addActionButton(function ($item) {
                            return '<a href="department/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever. ( FLEX360 can bring it back for you )\')" class="btn btn-secondary btn-sm">Delete</a>';
                        });
                        if (config('pilot.plugins.employees.children.departments.fields.sort_method') == 'manual_sort') {
                            $dynamo->indexOrderBy('position');
                        } else {
                            $dynamo->indexOrderBy('name');
                        }

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
