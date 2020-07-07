<?php

namespace Flex360\Pilot\Pilot;

use Illuminate\Support\Facades\Artisan;
use Spatie\MediaLibrary\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    protected $appends = ['full_url', 'conversion_small', 'temp'];

    public function getFullUrlAttribute($value)
    {
        return $this->getFullUrl();
    }

    public function getConversionSmallAttribute($value)
    {
        return $this->getFullUrl('small');
    }

    public function setTempAttribute($value)
    {
        $this->attributes['temp'] = $value;
    }

    public function getTempAttribute($value)
    {
        return isset($this->attributes['temp']) ? $this->attributes['temp'] : false;
    }

    public static function moveMediaItems($item, $mediaItems = [], $data = [], $clearMedia = true)
    {
        $updatedItems = collect();

        if (!empty($item) && $clearMedia) {
            $item->clearMediaCollection($data['collection_name']);
        }

        foreach ($mediaItems as $mediaItem) {
            $media = Media::find($mediaItem['id']);

            if ($media->model_type == 'Flex360\\Pilot\\Pilot\\Site') {
                // move the item
                $media->fill($data);

                if (!empty($item)) {
                    $media->save();
                    // regenerate possible missing conversions
                    Artisan::call('medialibrary:regenerate', [
                        '--ids' => $media->id,
                        '--only-missing' => true,
                    ]);
                } else {
                    $media->temp = true;
                }

                $updatedItems->push($media);
            } else {
                //copy the item
                if (!empty($item)) {
                    $tempFilePath = '/tmp/' . rand(10000, 99999) . '_' . $media->file_name;

                    copy($media->getPath(), $tempFilePath);

                    $copiedMediaItem = $item
                                       ->addMedia($tempFilePath)
                                       ->usingName($media->name)
                                       ->usingFileName($media->file_name)
                                       ->toMediaCollection($data['collection_name']);
                } else {
                    $media->temp = true;
                    $copiedMediaItem = $media;
                }

                $updatedItems->push($copiedMediaItem);
            }
        }

        return $updatedItems;
    }

    public function url()
    {
        return $this->getUrl();
    }

    public function meta($key)
    {
        return $this->getCustomProperty($key);
    }

    public function copyTo($item)
    {
        return $item->addMediaFromUrl($this->getFullUrl())
                    ->usingName($this->name)
                    ->usingFileName($this->file_name)
                    ->toMediaCollection($this->collection_name);
    }
}
