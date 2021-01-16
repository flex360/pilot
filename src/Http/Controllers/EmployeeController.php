<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Flex360\Pilot\Facades\Page as PageFacade;
use Flex360\Pilot\Pilot\Department;

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
        $employees = EmployeeFacade::with('departments')->orderBy('first_name');

        PageFacade::mimic([
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

        PageFacade::mimic([
            'title' => $department->name . 'Department'
        ]);

        return view('pilot::frontend.employees.departmentLandingPage', compact('department', 'employees', 'tags', 'resources'));
    }
}
