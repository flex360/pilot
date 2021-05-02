<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Scopes\PublishedScope;
use Flex360\Pilot\Facades\Project as ProjectFacade;
use Flex360\Pilot\Facades\ProjectCategory as ProjectCategoryFacade;

class ProjectController extends Controller
{
    // index view shows this individual product
    public function index()
    {
        $projectCategories = ProjectCategoryFacade::with('projects', 'projects.project_categories')->orderBy('name')->get();

        mimic([
            'title' => 'projects',
            'meta_description' => 'Find different projects by category.'
        ]);

        return view('pilot::frontend.projects.index', compact('projectCategories'));
    }

    public function categoryIndex($id, $slug)
    {
        $category = ProjectCategoryFacade::withoutGlobalScope(PublishedScope::class)->find($id);

        mimic($category->name);

        return view('pilot::frontend.projects.categoryIndex', compact('category'));
    }
    
    // detail view shows this individual project
    public function detail($id, $slug)
    {
        $project = ProjectFacade::withoutGlobalScope(PublishedScope::class)->find($id);
        
        mimic($project->title);

        return view('pilot::frontend.projects.detail', compact('project'));
    }
    
}
