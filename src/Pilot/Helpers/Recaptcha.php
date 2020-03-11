<?php

namespace Flex360\Pilot\Pilot\Helpers;

use Asset;

class Recaptcha
{
    public static function init()
    {
        Asset::js('https://www.google.com/recaptcha/api.js');
    }

    public static function render($callback = null)
    {
        return '<div class="g-recaptcha" data-sitekey="' .
                env('RECAPTCHA_SITE_KEY') . '"' .
                (! empty($callback) ? ' data-callback="' . $callback . '"' : '') .
                '></div>';
    }

    public static function inputExcept()
    {
        $params = func_get_args();

        $params[] = 'g-recaptcha-response';

        return request()->except($params);
    }

    public static function isValid($string = null)
    {
        if (empty($string)) {
            $string = request()->input('g-recaptcha-response');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'secret' => env('RECAPTCHA_SECRET_KEY'), 'response' => $string)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $google = curl_exec($ch);

        curl_close($ch);

        return json_decode($google)->success;
    }
}
