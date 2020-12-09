<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Department;
use Flex360\Pilot\Pilot\Employee;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\ResourceCategory;

class EmployeeController extends Controller
{
    /**
     * Load /employees page;
     *
     * @return View 
     */
    public function index()
    {
        //get all resource categories, order by name
        $employees = Employee::with('departments')->orderBy('first_name');

        Page::mimic([
            'title' => 'Employees'
        ]);

        return view('pilot::frontend.employees.index', compact('employees'));
    }

    /**
     * Load /department/{department}/{slug} page;
     *
     * @return View 
     */
    public function departmentLandingPage(Department $department)
    {
        //get all resource categories, order by name
        $employees = $department->employees();
        $tags = $department->tags();
        $resources = $department->resources();

        Page::mimic([
            'title' => $department->name . 'Department'
        ]);

        return view('pilot::frontend.employees.departmentLandingPage', compact('department', 'employees', 'tags', 'resources'));
    }
}
