<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use App\Http\Requests;
use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Jzpeepz\Dynamo\FieldGroup;
use App\Http\Controllers\Controller;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use Flex360\Pilot\Facades\Annoucement as AnnoucementFacade;

class AnnoucementController extends DynamoController
{
    public function getDynamo()
    {
        $dynamo = Dynamo::make(get_class(AnnoucementFacade::getFacadeRoot()));
                    //check if display_name is used, if so, use the dynamo alias function to change the name everywhere at once
                    if (config('pilot.plugins.annoucements.display_name') != null) {
                        $dynamo->alias(Str::singular(config('pilot.plugins.annoucements.display_name')));
                    }

                    

                    /************************************************************************************
                     *  Pilot plugin: Annoucement form view                                            *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    if (config('pilot.plugins.annoucements.fields.headline', true)) {
                        $dynamo->text('headline', [
                            'help' => 'Suggest keeping to 5 words or less',
                            'attributes' => ['id' => 'headline']
                        ]);
                    }
                    if (config('pilot.plugins.annoucements.fields.short_description', true)) {
                        $dynamo->text('short_description', [
                            'help' => 'Not required. Suggest keeping to 10 words or less if used.'
                        ]);
                    }
                    if (config('pilot.plugins.annoucements.fields.button_text', true)) {
                        $dynamo->text('button_text', [
                            'help' => 'If you want the announcement to link somewhere, what do you want the button to say? "Read More" is a good go-to.'
                        ]);
                    }
                    if (config('pilot.plugins.annoucements.fields.button_link', true)) {
                        $dynamo->text('button_link', [
                            'help' => 'If you want the announcement to link somewhere, paste the path (internal links) or entire URL (external links) here.'
                        ]);
                    }
                    if (config('pilot.plugins.annoucements.fields.status', true)) {
                        $dynamo->checkbox('status', [
                            "label" => "Check this box to activate this Annoucement!",
                        ]);
                    }
                    $dynamo->removeBoth('deleted_at');
                    


                    /************************************************************************************
                     *  Pilot plugin: Annoucement index view                                           *
                     *  Check the plugins 'fields' array and attach the fields to the dynamo object    *
                     ************************************************************************************/
                    $dynamo->clearIndexes()
                    ->addIndexButton(function () {
                        return '<a href="/pilot/annoucement/deactivate" class="btn btn-danger btn-sm">Deactivate</a>';
                    });
                    if (config('pilot.plugins.annoucements.fields.headline', true)) {
                        $dynamo->addIndex('headline');
                    }
                    if (config('pilot.plugins.annoucements.fields.short_description', true)) {
                        $dynamo->addIndex('short_description');
                    }
                    if (config('pilot.plugins.annoucements.fields.button_text', true)) {
                        $dynamo->addIndex('button_text');
                    }
                    if (config('pilot.plugins.annoucements.fields.button_link', true)) {
                        $dynamo->addIndex('button_link');
                    }
                    if (config('pilot.plugins.annoucements.fields.button_link', true)) {
                        $dynamo->addIndex('updated_at', 'Last Edited');
                    }
                    if (config('pilot.plugins.annoucements.fields.status', true)) {
                        $dynamo->addIndex('active', 'Status', function ($annoucement) {
                            if ($annoucement->status) {
                                return '<span class="badge alert-success">Active</span';
                            } else {
                                return '<span class="badge alert-danger">Inactive</span';
                            }
                        });
                    }
                    $dynamo->addActionButton(function ($annoucement) {
                        if (!$annoucement->status) {
                            return '<a href="' . route('admin.annoucement.activate', $annoucement->id) . '" class="btn btn-secondary btn-sm"> Activate</a>';
                        }

                        return '';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="annoucement/' . $item->id . '/copy" class="btn btn-secondary btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '<a href="annoucement/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')" class="btn btn-secondary btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('headline');


        return $dynamo;
    }

    /**
    * Active the Annoucement
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function activate($id)
    {
        $annoucement = AnnoucementFacade::find($id);
        $annoucement->status = 1;
        $annoucement->save();

        \Session::put('alert-success', $annoucement->headline.' has been activated!');

        return redirect()->route('admin.annoucement.index');
    }

    /**
    * Copy the Annoucement
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function copy($id)
    {
        $annoucement = AnnoucementFacade::find($id);

        $newAnnoucement = $annoucement->duplicate();

        // set success message
        \Session::flash('alert-success', 'Annoucement copied successfully!');

        return redirect()->route('admin.annoucement.edit', [$newAnnoucement->id]);
    }

    /**
    * Delete the Annoucement
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $annoucement = AnnoucementFacade::find($id);

        $annoucement->delete();

        // set success message
        \Session::flash('alert-success', 'Annoucement deleted successfully!');

        return redirect()->route('admin.annoucement.index');
    }

    /**
    * Deactivate all annoucements
    *
    * @return \Illuminate\Http\Response
    */
    public function deactivate()
    {
        $annoucements = AnnoucementFacade::all();

        foreach ($annoucements as $annoucement) {
            $annoucement->status = false;
            $annoucement->save();
        }

        // set success message
        \Session::flash('alert-success', 'Annoucements deactivated successfully!');

        return redirect()->route('admin.annoucement.index');
    }
}
