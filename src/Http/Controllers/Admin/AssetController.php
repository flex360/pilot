<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Flex360\Pilot\Pilot\Asset;
use Illuminate\Support\Facades\Validator;
use Flex360\Pilot\Http\Controllers\Controller;

class AssetController extends Controller
{
    public function getAll()
    {
        $assetPath = public_path() . '/assets/uploads/' . date('Y/m');

        if (\File::isDirectory($assetPath)) {
            $allFiles = \File::allFiles($assetPath);
        } else {
            $allFiles = [];
        }

        $files = [];

        foreach ($allFiles as $file) {
            $absolutePath = (string) $file;
            $relativePath = str_replace(public_path(), '', $absolutePath);
            $files[] = asset($relativePath);
        }

        return \Response::json($files);
    }

    public function postUpload()
    {
        $uploadPath = Asset::initUploadDirectory();

        // grab the file
        $file = request()->file('file');
        $output = [];
        parse_str(request()->input('params'), $output);
        // make clean filename
        $fileName = Asset::cleanFilename($file->getClientOriginalName(), $file->getClientOriginalExtension());

        // check extension
        $validExt = in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx']) ? true : false;

        // validate the file being uploaded
        $rules = ['file' => 'required|mimes:png,gif,jpeg,txt,pdf,doc'];

        $validator = Validator::make(['file' => $file], $rules);

        if ($validator->passes() && $validExt) {
            // move the file to the final directory
            $file->move($uploadPath, $fileName);

            $publicPath = str_replace(storage_path('app/public'), '/storage', $uploadPath) . '/' . $fileName;

            // if this is an image, try to resize it
            if (in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'])) {
                $img = Asset::manipulateImage($uploadPath . '/' . $fileName, $output);
            }

            if (request()->has('type') && request()->input('type') == 'trumbowyg') {
                return response()->json(['success' => true, 'file' => $publicPath]);
            }

            return response()->json(['link' => $publicPath]);
        }
    }
}
