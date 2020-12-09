<?php

namespace Flex360\Pilot\Http\Controllers\Admin; 

use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Jzpeepz\Dynamo\IndexTab;
use Flex360\Pilot\Pilot\Resource;
use Flex360\Pilot\Pilot\ResourceCategory;

class ResourceController extends DynamoController
{
    public function getDynamo()
    {
        return Dynamo::make(Resource::class)
            ->auto()
            
            ->removeBoth('featured_image')
            ->addIndexButton(function () {
                return '<a href="/pilot/resourcecategory?view=all" class="btn btn-primary btn-sm">Resource Categories</a>';
            })
            // ->singleImage("featured_image", [
            //     'tooltip' =>  'Let\'s you select one image',
            //     'maxWidth' => 1000,
            // ])
            ->text('title')
            ->text('short_description')
            ->singleFileOrUrl('link', [
                'label' => 'Upload or Link Resource',
                'help' => '<strong>Need to upload a <span style="text-decoration: underline;">PDF, spreadsheet, or other document</span>?</strong><br> <i>--Select \'\'File\'\' and use the 
                             uploader tool or browse other files already uploaded to the website.</i><Br>
                             
                             <strong>Need to link to a page or section of the <span style="text-decoration: underline;">this</span> website?</strong><br> <i>--Select \'\'URL\'\' and paste the 
                             path to the desired resource. The \'\'path\'\' is whatever comes after the main URL known as the domain name.
                             Example: To link to an about-us page, enter \'\'/about-us\'\'</i><br>
                             
                             <strong>Need to link to a webpage or a document from <span style="text-decoration: underline;">external</span> website?</strong><br> <i>--Enter the entire web address.
                             Example: If you were linking to an article on a news site, you would enter
                             \'\'https://newsWebsite.com/article/4/name-of-article\'\'</i>',
            ])
            ->hasManySimple('resource_categories', [
                'label' => 'Resource Categories',
                'help' => '<mark><strong>REQUIRED: </strong> If left blank, this resource will not display on the website.</mark> Click in the bar or begin typing and select the appropriate category(ies) from the list.<br>
                            
                             --If the category(ies) you need do not exist, save resource as a draft and come back to it 
                             after adding the category in the <a href="/pilot/resourcecategory" target="_blank">Resource Category Manager</a>.',
                'options' => ResourceCategory::getSelectList(),
                'class' => 'chosen-select',
                'value' => function ($item, $field) {
                    return $item->{$field->key}()->withoutGlobalScopes()->pluck('id')->toArray();
                },
            ])
            ->select('status', [
                'options' => Resource::getStatuses(),
                'help' => 'Save a draft to come back to this later. Published resources will be automatically displayed on the front-end of the website after you save.',
                'position' => 500,
            ])
            ->removeField('deleted_at')

            //set index view
            // ->applyScopes()
            ->indexTab(
                IndexTab::make('Published', function ($query) {
                    return $query->where('status', 30)->orderBy('title');
                })
                    ->setBadgeColor('blue') // default is red if you don't supply
                    ->showCount()
            )
            ->indexTab(
                IndexTab::make('Drafts', function ($query) {
                    return $query->where('status', 10);
                })
                    ->showCount()
            )
            ->paginate(25)
            ->searchable('title')
            ->searchOptions([
                'placeholder' => 'Search By Title',
            ])
            ->clearIndexes()
            ->addIndex('title')
            ->addIndex('short_description', 'Short Description', function ($resource) {
                return strlen($resource->short_description) > 50 ? substr($resource->short_description,0,50)."..." : $resource->short_description;
            })
            ->addIndex('category', 'Categories', function ($resource) {
                return $resource->resource_categories()->get()->transform(function ($cat) {
                    return $cat->name;
                })
                    ->implode(', ');
            })
            ->addIndex('updated_at', 'Last Edited')
            ->addActionButton(function($item) {
                return '<a href="resource/' . $item->id . '/copy"  class="btn btn-secondary btn-sm">Copy</a>';
            })
            ->addActionButton(function ($item) {
                return '<a href="resource/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')"  class="btn btn-secondary btn-sm">Delete</a>';
            })
            ->indexOrderBy('title');
    }

    /**
     * Copy the Resource
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        $resource = Resource::find($id);

        $newResource = $resource->duplicate();

        // set success message
        \Session::flash('alert-success', 'Resource copied successfully!');

        return redirect()->route('admin.resource.edit', array($newResource->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resource = Resource::find($id);

        $resource->delete();

        // set success message
        \Session::flash('alert-success', 'Resource deleted successfully!');

        return \Redirect::to('/pilot/resource?view=published');
    }
}
