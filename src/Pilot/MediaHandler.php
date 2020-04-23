<?php

namespace Flex360\Pilot\Pilot;

use Jzpeepz\Dynamo\Dynamo;

class MediaHandler
{
    public function get()
    {
        return function (&$item, &$data, $key, $clearMedia = true) {
            $files = isset($data[$key]) ? $data[$key] : [];

            // check to see if this field has media items that need to be moved
            // this handles media added to objects that are being created
            // (as opposed to those being updated)
            if (request()->has($key)) {
                $mediaIdsToMove = request()->input($key);

                // if this is an array of ids
                if (is_array($mediaIdsToMove) && is_numeric(current($mediaIdsToMove))) {
                    // convert the array of ids to an array of arrays containing the id
                    $mediaIdsToMove = array_map(function ($mediaId) {
                        return [
                            'id' => $mediaId
                        ];
                    }, $mediaIdsToMove);

                    // build up the data that is need to move the media
                    $mediaData = [
                        'model_id' => $item->id,
                        'model_type' => get_class($item),
                        'collection_name' => $key,
                    ];

                    // remove the from the data array to prevent errors
                    unset($data[$key]);

                    return Media::moveMediaItems($item, $mediaIdsToMove, $mediaData);
                }
            }

            // convert data to an array if not already
            if (!is_array($files)) {
                $files = [$files];
            }

            if ($clearMedia) {
                $item->clearMediaCollection($key);
            }

            $addedMedia = collect();

            foreach ($files as $file) {
                $media = $item->addMedia($file->getRealPath())
                           ->usingName($file->getClientOriginalName())
                           ->usingFileName($file->getClientOriginalName())
                           ->toMediaCollection($key);

                $addedMedia->push($media);
            }

            unset($data[$key]);

            return $addedMedia;
        };
    }

    public static function register()
    {
        app()->bind('App\Pilot\MediaHandler', function ($app) {
            return new MediaHandler;
        });

        $fileHandler = (new MediaHandler)->get();

        Dynamo::addGlobalHandler('singleImage', $fileHandler);

        Dynamo::addGlobalHandler('singleFile', $fileHandler);

        Dynamo::addGlobalHandler('singleFileOrUrl', function (&$item, &$data, $key) use ($fileHandler) {
            if (is_string($data[$key])) {
                // do nothing
            } else {
                call_user_func_array($fileHandler, [&$item, &$data, $key]);
                $data[$key] = null;
            }
        });

        Dynamo::addGlobalHandler('gallery', function (&$item, &$data, $key) use ($fileHandler) {
            call_user_func_array($fileHandler, [&$item, &$data, $key, false]);
        });
    }
}
