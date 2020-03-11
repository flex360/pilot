<?php

namespace Flex360\Pilot\Pilot;

use Image;
use Illuminate\Support\Str;

class Asset
{
    public static $assets = array();

    public static $templates = array(
        'css' => '<link rel="stylesheet" href="%s" media="%s">',
        'js' => '<script src="%s"></script>'
    );

    public static function add($type, $path, $media = 'all')
    {
        self::$assets[$type][] = array('path' => $path, 'media' => $media);
    }

    public static function css($path, $media = 'all')
    {
        self::add('css', $path, $media);
    }

    public static function js($path)
    {
        self::add('js', $path);
    }

    public static function link($type)
    {
        $html = '';

        if (isset(self::$assets[$type])) {
            foreach (self::$assets[$type] as $asset) {
                $html .= sprintf(self::$templates[$type], $asset['path'], $asset['media']) . "\n\r";
            }
        }

        return $html;
    }

    public static function linkCSS()
    {
        return self::link('css');
    }

    public static function linkJS()
    {
        return self::link('js');
    }

    public static function getUploadDirectory()
    {
        return self::initUploadDirectory();
    }

    public static function initUploadDirectory()
    {
        // make asset uploads folder id needed
        $uploadPath = public_path() . '/assets/uploads';

        if (! \File::isDirectory($uploadPath)) {
            \File::makeDirectory($uploadPath);
        }

        // make account specific upload folder id needed
        // $uploadPath .= '/' . \Account::getCurrent('username');

        // if (! \File::isDirectory($uploadPath))
        // {
        //     \File::makeDirectory($uploadPath);
        // }

        // make yearly upload directory if needed
        $uploadPath .= '/' . date('Y');

        if (! \File::isDirectory($uploadPath)) {
            \File::makeDirectory($uploadPath);
        }

        // add on month directory
        $uploadPath .= '/' . date('m');

        // make monthly upload directory if needed
        if (! \File::isDirectory($uploadPath)) {
            \File::makeDirectory($uploadPath);
        }

        return $uploadPath;
    }

    public static function cleanFilename($filename, $extension)
    {
        return date('YmdHis') . '-' . Str::slug($filename) . '.' . strtolower($extension);
    }

    public static function uploadMultiple($files)
    {
        $uploadPath = self::initUploadDirectory();

        $publicPath = str_replace(public_path(), '', $uploadPath) . '/';

        $paths = array();

        foreach ($files as $file) {
            $newFilename = self::cleanFilename($file->getClientOriginalName(), $file->getClientOriginalExtension());

            $file->move($uploadPath, $newFilename);

            $img = Asset::manipulateImage($uploadPath.'/'.$newFilename);

            $paths[] = array('path' => $publicPath . $newFilename);
        }

        return $paths;
    }

    public static function manipulateImage($path, $output = [])
    {
        $image = Image::make($path);

        if (! isset($output['height']) && ! isset($output['width'])) {
            $maxWidth = 2000;

            if ($image->width() < $maxWidth) {
                $maxWidth = $image->width();
            }

            $output['width'] = $maxWidth;
        }

        if (! isset($output['height']) && isset($output['width'])) {
            // calcuate height
            $targetWidth = $output['width'];
            $targetHeight = ceil($image->height() * $targetWidth / $image->width());
            $output['height'] = $targetHeight;
        }

        if (! isset($output['width']) && isset($output['height'])) {
            // calcuate width
            $targetHeight = $output['height'];
            $targetWidth = ceil($image->width() * $targetHeight / $image->height());
            $output['width'] = $targetWidth;
        }

        return $image->fit($output['width'], $output['height'])->save($path);
    }
}
