<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Http\Controllers\Controller;
use Flex360\Pilot\Pilot\Forms\Wufoo\WufooForm;

class FormController extends Controller
{

    public function index()
    {
        $forms = WufooForm::getForms();

        return view('admin.forms.index', compact('forms'));
    }

    public function entries($hash)
    {
        $wufoo = WufooForm::make($hash);

        $wufoo->makeMagic();

        $entries = \MagicWufooFormEntry::orderBy('EntryId', 'desc')->paginate(25);

        $columns = $wufoo->getColumns(5);

        return view('admin.forms.entries', compact('wufoo', 'entries', 'columns'));
    }

    public function entry($hash, $id)
    {
        $wufoo = WufooForm::make($hash);

        $wufoo->makeMagic();

        $entry = \MagicWufooFormEntry::find($id);

        return view('admin.forms.entry', compact('wufoo', 'entry'));
    }

    public function webhook($hash)
    {
        $wufoo = WufooForm::make($hash)->makeMagic();

        $data = request()->all();

        // dd([$data['HandshakeKey'], $wufoo->getHandshakeKey()]);

        if ($data['HandshakeKey'] == $wufoo->getHandshakeKey()) {
            unset($data['HandshakeKey']);
            unset($data['IP']);

            \MagicWufooFormEntry::create($data);
        }
    }

    public function configuration($hash)
    {
        $form = WufooForm::make($hash);

        return view('admin.forms.configuration', compact('form'));
    }

    public function sync($hash)
    {
        $form = WufooForm::make($hash);

        $form->sync();
    }
}
