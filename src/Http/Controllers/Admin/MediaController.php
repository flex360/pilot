<?php

namespace Flex360\Pilot\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Flex360\Pilot\Pilot\Site;
use Flex360\Pilot\Pilot\Media;
use Illuminate\Support\Facades\DB;
use Flex360\Pilot\Pilot\MediaHandler;
use Illuminate\Support\Facades\Artisan;
use Flex360\Pilot\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function __construct(MediaHandler $mediaHandler)
    {
        $this->fileHandler = $mediaHandler->get();
    }

    public function info($id)
    {
        $data = request()->except(['_token']);

        $media = Media::find($id);

        foreach ($data as $key => $value) {
            $media->setCustomProperty($key, $value);
        }

        $media->save();
    }

    public function destroy($id)
    {
        $media = Media::find($id);

        $media->delete();
    }

    public function fix()
    {
        $storagePath = storage_path('app/public');

        $files = scandir($storagePath);

        $mediaIdsWithoutConversions = [];

        foreach ($files as $file) {
            $filePath = $storagePath . '/' . $file;
            $isDir = is_dir($filePath) && $file != '.' && $file != '..';
            if ($isDir && !is_dir($filePath . '/conversions')) {
                array_push($mediaIdsWithoutConversions, $file);
            }
        }

        // dd($mediaIdsWithoutConversions);

        $media = Media::whereIn('id', $mediaIdsWithoutConversions)->get();

        return view('pilot::admin.media.fix', compact('media'));
    }

    public function replace()
    {
        $mediaId = request()->input('mediaId');

        $file = request()->file('file');

        $media = Media::find($mediaId);

        $mediaPath = 'public/' . $media->id;

        $result = $file->storeAs($mediaPath, $media->file_name);

        // define('STDIN', null); // fix an error caused when STDIN not defined
        $exitCode = Artisan::call('medialibrary:regenerate', [
            'modelType' => $media->model_type,
            '--only-missing' => true,
            '--ids' => $media->id,
            '--force' => true,
        ]);
    }

    public function order()
    {
        $ids = request()->input('ids');

        Media::setNewOrder($ids);
    }

    public function getTypes()
    {
        $types = DB::table('media')
                    ->select('model_type')
                    ->distinct()
                    ->where('model_type', '!=', 'Flex360\Pilot\Pilot\Site')
                    ->orderBy('model_type')
                    ->pluck('model_type')
                    ->transform(function ($class) {
                        $classParts = explode('\\', $class);

                        return [
                            'model_type' => $class,
                            'name' => Str::plural(last($classParts))
                        ];
                    })
                    ->sortBy('name');

        $types->prepend([
            'model_type' => 'Flex360\Pilot\Pilot\Site',
            'name' => 'Unassigned',
        ]);

        return response()->json($types);
    }

    public function getByType()
    {
        $type = request()->input('type');

        $media = Media::where('model_type', $type)
                        // ->groupBy('model_type', 'file_name')
                        ->orderBy('id', 'desc')
                        ->get();

        return $media->filter(function ($mediaItem) {
            return file_exists($mediaItem->getPath('small'));
        })->unique('file_name');
    }

    public function rename()
    {
        $id = request()->input('id');

        $newName = request()->input('newName');

        $media = Media::find($id);

        $media->name = $newName;

        $media->save();

        return $media;
    }

    public function upload()
    {
        $file = request()->file('file');

        $item = Site::find(1);

        $key = 'general';

        $data = [$key => $file];

        $mediaItems = call_user_func_array($this->fileHandler, [&$item, &$data, $key, false]);

        return collect($mediaItems->first());
    }

    public function move()
    {
        $mediaItems = request()->input('mediaItems');

        $clearMedia = (bool) request()->input('clear_media');

        $data = [
            'model_id' => request()->input('model_id'),
            'model_type' => request()->input('model_type'),
            'collection_name' => request()->input('collection'),
        ];

        $item = ('\\' . $data['model_type'])::withoutGlobalScopes()->find($data['model_id']);

        return Media::moveMediaItems($item, $mediaItems, $data, $clearMedia);
    }

    public function search()
    {
        $q = request()->input('query');

        return Media::where(function ($query) use ($q) {
            $query->where('name', 'like', '%' . $q . '%')
                                  ->orWhere('file_name', 'like', '%' . $q . '%')
                                  ->orWhere('custom_properties', 'like', '%' . $q . '%');
        })
                        ->groupBy('model_type', 'file_name')
                        ->orderBy('id', 'desc')
                        ->get();
    }
}
