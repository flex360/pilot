<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\IndexTab;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Pilot\Resource;
use Flex360\Pilot\Pilot\ResourceCategory;

class ResourceCategoryController extends DynamoController
{
    public function getDynamo()
    {
        return Dynamo::make(ResourceCategory::class)
            ->auto()
            ->addIndexButton(function () {
                return '<a href="/resources" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>';
            })
            ->addIndexButton(function () {
                return '<a href="/pilot/resource?view=published" class="btn btn-info btn-sm">Back to Resources</a>';
            })
            ->addActionButton(function ($item) {
                return '<a href="product/' . $item->id . '/delete" data-toggle="modal" data-target="#relationships-manager-modal" class="btn btn-secondary btn-sm">Delete</a>';
            })
            ->hideDelete()
            ->removeBoth('deleted_at')

            ->text('name')
            ->hasMany('resources', [
                'options' => Resource::orderBy('title')->pluck('title', 'id'),
                'class' => 'category-dual-list',
                'id' => 'category-dual-list',
                'help' => 'Select the Resources you would like to belong to this category.',
            ])
            ->paginate(25)
            ->addIndex('test', 'Number of resources in this category', function ($item) {
                return $item->resources->count();
            })
            ->addActionButton(function($item) {
                return '<a href="resourcecategory/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
            })
            ->indexOrderBy('name');
    }

    /**
     * Copy the Resource Category
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $cat = ResourceCategory::find($id);

        $newCat = $cat->duplicate();

        // set success message
        \Session::flash('alert-success', 'Category copied successfully!');

        return redirect()->route('admin.resourcecategory.edit', array($newCat->id));
    }

    /**
     * Remove the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $category = ResourceCategory::find($id);

        $category->delete();

        // set success message
        \Session::flash('alert-success', 'Category deleted successfully!');

        return \Redirect::route('admin.resourcecategory.index');
    }
}
