<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use App\Http\Requests;

use Jzpeepz\Dynamo\Dynamo;
use Illuminate\Http\Request;
use Jzpeepz\Dynamo\FieldGroup;
use App\Http\Controllers\Controller;
use Flex360\Pilot\Pilot\Annoucement;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;

class AnnoucementController extends DynamoController
{
    public function getDynamo()
    {
        return Dynamo::make(Annoucement::class)
                    ->auto()

                    // form
                    ->text('headline', [
                        'help' => 'Suggest keeping to 5 words or less'
                    ])
                    ->text('short_description', [
                        'help' => 'Not required. Suggest keeping to 10 words or less if used.'
                    ])
                    ->text('button_text', [
                        'help' => 'If you want the announcement to link somewhere, what do you want the button to say? \'\'Read More\'\' is a good go-to.'
                    ])
                    ->text('button_link', [
                        'help' => 'If you want the announcement to link somewhere, paste the path (internal links) or entire URL (external links) here.'
                    ])
                    ->checkbox('status', [
                        "label" => "Check this box to activate this Annoucement!",
                    ])
                    ->removeBoth('deleted_at')
                    

                    //index
                    ->clearIndexes()
                    ->addIndexButton(function () {
                        return '<a href="/pilot/annoucement/deactivate" class="btn btn-danger btn-sm">Deactivate</a>';
                    })
                    ->addIndex('headline')
                    ->addIndex('short_description')
                    ->addIndex('button_text')
                    ->addIndex('button_link')
                    ->addIndex('updated_at', 'Last update')
                    ->addIndex('active', 'Status', function ($annoucement) {
                        if ($annoucement->status) {
                            return '<span class="badge alert-success">Active</span';
                        } else {
                            return '<span class="badge alert-danger">Inactive</span';
                        }
                    })
                    ->addActionButton(function ($annoucement) {
                        if (!$annoucement->status) {
                            return '|' . '<a href="' . route('admin.annoucement.activate', $annoucement->id) . '" style="padding: 10px 5px!important;" class="btn btn-link btn-sm"> Activate</a>';
                        }

                        return '';
                    })
                    ->addActionButton(function ($item) {
                        return '| ' . '<a href="annoucement/' . $item->id . '/copy" style="padding: 10px 0px!important;" class="btn btn-link btn-sm">Copy</a>';
                    })
                    ->addActionButton(function ($item) {
                        return '| ' . '<a href="annoucement/' . $item->id . '/delete" onclick="return confirm(\'Are you sure you want to delete this? This action cannot be undone and will be deleted forever.\')" style="padding: 10px 0px!important;" class="btn btn-link btn-sm">Delete</a>';
                    })
                    ->indexOrderBy('headline');
    }

    /**
    * Active the Annoucement
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function activate($id)
    {
        $annoucement = Annoucement::find($id);
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
        $annoucement = Annoucement::find($id);

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
        $annoucement = Annoucement::find($id);

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
        $annoucements = Annoucement::all();

        foreach ($annoucements as $annoucement) {
            $annoucement->status = false;
            $annoucement->save();
        }

        // set success message
        \Session::flash('alert-success', 'Annoucements deactivated successfully!');

        return redirect()->route('admin.annoucement.index');
    }
}
