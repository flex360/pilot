<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Jzpeepz\Dynamo\IndexTab;
use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;

class EmployeeController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(EmployeeFacade::getFacadeRoot()));
                    //check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.employees.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.employees.display_name')));
                    }



                    /************************************************************************************
                     *  Pilot plugin: Employee form view                                               *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    if (config('pilot.plugins.employees.fields.photo', true)) {
                        $dynamo->singleImage('photo', [
                            'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                            'maxWidth' => 1000,
                        ]);
                    }
                    if (config('pilot.plugins.employees.fields.first_name', true)) {
                        $dynamo->text('first_name');
                    }
                    if (config('pilot.plugins.employees.fields.last_name', true)) {
                        $dynamo->text('last_name');
                    }   
                    if (config('pilot.plugins.employees.fields.start_date', true)) {
                        $dynamo->text('start_date', [
                            'class' => 'datepicker'
                        ]);
                    }      
                    if (config('pilot.plugins.employees.fields.birth_date', true)) {
                        $dynamo->text('birth_date', [
                            'class' => 'datepicker'
                        ]);
                    }
                    if (config('pilot.plugins.employees.fields.job_title', true)) {
                        $dynamo->text('job_title');
                    }
                    if (config('pilot.plugins.employees.fields.office_phone', true)) {
                        $dynamo->text('phone_number', [
                            'label' => 'Office Phone',
                        ]);
                    }
                    if (config('pilot.plugins.employees.fields.cell_phone', true)) {
                        $dynamo->text('cell_number', [
                            'label' => 'Cell Phone',
                        ]);
                    }
                    if (config('pilot.plugins.employees.fields.extension', true)) {
                        $dynamo->text('extension');
                    }
                    if (config('pilot.plugins.employees.fields.email', true)) {
                        $dynamo->text('email');
                    }
                    if (config('pilot.plugins.employees.fields.office_location', true)) {
                        $dynamo->text('office_location');
                    }
                    if (config('pilot.plugins.employees.fields.bio', true)) {
                        $dynamo->textarea('bio', [
                            'class' => 'wysiwyg-editor'
                        ]);
                    }
                    if (config('pilot.plugins.employees.children.departments.enabled', true)) {
                        $dynamo->hasManySimple('departments', [
                            'modelClass' => 'Flex360\Pilot\Pilot\Department',
                            'help' => 'Departments must already exist. If they don\'t, please save this Employee as a draft without assigned departments
                                        and go to the <a href="/pilot/department?view=published" target="_blank">Department Manager</a> to create the desired department.',
                        ]);
                    }
                    if (config('pilot.plugins.employees.fields.status', true)) {
                        $dynamo->select('status', [
                            'options' => EmployeeFacade::getStatuses(),
                            'help' => 'Use the "Draft" status to save information as you have it. When you\'re ready for a Employee to
                                        show up on the front end of the website, change it to "Published" and then click the "Save Employee" button.',
                            'position' => 500,
                        ]);
                    }
                    $dynamo->removeField('position');
                    $dynamo->removeField('deleted_at');




                    /************************************************************************************
                     *  Pilot plugin: Employee index view                                              *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    $dynamo->applyScopes()
                    ->clearIndexes();
                    if (config('pilot.plugins.employees.children.departments.enabled', true)) {
                        $dynamo->addIndexButton(function() {
                            return '<a href="/pilot/department" class="btn btn-primary btn-sm">Departments</a>';
                        });
                    }
                    $dynamo->paginate(50)
                    ->indexTab(
                        IndexTab::make('Published', function ($query) {
                            return $query->where('status', 30)->whereNull('deleted_at');
                        })
                        ->setBadgeColor('blue') // default is red if you don't supply
                        ->showCount()
                    )

                    ->indexTab(
                        IndexTab::make('Drafts', function ($query) {
                            return $query->where('status', 10)->whereNull('deleted_at');
                        })
                        ->showCount()
                    )
                    ->searchable('first_name')
                    ->searchOptions([
                        'placeholder' => 'Search By Name',
                    ]);
                    if (config('pilot.plugins.employees.fields.photo', true)) {
                        $dynamo->addIndex('photo', 'Photo', function ($item) {
                            if (empty($item->photo)) {
                                return '';
                            }
                            return '<img style="width: 100px  " src="' . $item->photo . '" class="" style="width: 60px;">';
                        });
                    }
                    if (config('pilot.plugins.employees.fields.first_name', true)) {
                        $dynamo->addIndex('first_name');
                    }
                    if (config('pilot.plugins.employees.fields.last_name', true)) {
                        $dynamo->addIndex('last_name');
                    }
                    if (config('pilot.plugins.employees.children.departments.enabled', true)) {
                        $dynamo->addIndex('departments', 'Departments', function ($item) {
                            return $item->departments->implode('name', ', ');
                        });
                    }

                    $dynamo->addActionButton(function ($item) {
                        return '<a href="employee/' . $item->id . '/copy" class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="employee/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever. ( FLEX360 can bring it back for you )\')" class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('last_name');



                    
        return $dynamo;
    }

    /**
     * Copy the Employee
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $employee = EmployeeFacade::find($id);

        $newEmployee = $employee->duplicate();

        // set success message
        \Session::flash('alert-success', 'Employee copied successfully!');

        return redirect()->route('admin.employee.edit', [$newEmployee->id]);
    }

    /**
     * Remove the specified Employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = EmployeeFacade::find($id);

        $employee->delete();

        // set success message
        \Session::flash('alert-success', 'Employee deleted successfully!');

        return \Redirect::to('/pilot/employee?view=published');
    }
}
