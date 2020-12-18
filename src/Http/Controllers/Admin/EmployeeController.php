<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\IndexTab;
use Flex360\Pilot\Pilot\Employee;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;

class EmployeeController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(EmployeeFacade::getFacadeRoot()))
                    ->auto()
                    ->removeField('position')
                    ->removeField('deleted_at')
                    ->applyScopes()

                    ->singleImage('photo', [
                        'help' => 'Upload photo. Once selected, hover over the image and select the edit icon (paper & pencil) to manage metadata title, photo credit, and description.',
                        'maxWidth' => 1000,
                    ])
                    ->text('first_name')
                    ->text('last_name')
                    ->text('start_date', [
                        'class' => 'datepicker'
                    ])
                    ->text('birth_date', [
                        'class' => 'datepicker'
                    ])
                    ->text('job_title')
                    ->text('phone_number')
                    ->text('extension')
                    ->text('email')
                    ->text('office_location')
                    ->select('status', [
                        'options' => EmployeeFacade::getStatuses(),
                        'help' => 'Save a draft to come back to this later. Published Employees will be automatically displayed on the front-end of the website after you save.',
                        'position' => 500,
                    ])

                    //Set indexes for admin view
                    ->clearIndexes()
                    ->paginate(50)
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
                    ])
                    ->addIndex('photo', 'Photo', function ($item) {
                        if (empty($item->photo)) {
                            return '';
                        }
                        return '<img style="width: 100px  " src="' . $item->photo . '" class="" style="width: 60px;">';
                    })
                    ->addIndex('first_name')
                    ->addIndex('last_name')
                    ->addIndex('departments', 'Departments', function ($item) {
                        return $item->departments->implode('name', ', ');
                    })

                    ->addActionButton(function ($item) {
                        return '<a href="employee/' . $item->id . '/copy" class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="employee/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever. ( FLEX360 can bring it back for you )\')" class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('last_name');

        // if departments is enabled, include HasManySimple multi-selector
        if (config('pilot.plugins.employees.children.departments.enabled')) {
            $dynamo->hasManySimple('departments', [
                'modelClass' => 'Flex360\Pilot\Pilot\Department',
                'help' => 'Select the Departments this Employees belongs to. If you don\'t see the Department available, you will need to <a href="/pilot/department/create" target="_blank">create the Department</a>.'
            ]);
        }

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
