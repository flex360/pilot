<?php

namespace Flex360\Pilot\Pilot\Publish;

class Publish
{
    private static $key = 'H376jQNGma9vPdcgDbsf';
    public static $publish_type = null;

    public static function request($model, $params, $format = 'json')
    {
        $url = 'http://publish.flex360.com/api/' . $model . '.' . $format;
        $params['key'] = self::$key;

        // die($url .'?'. http_build_query($params));
        // echo($url .'?'. http_build_query($params));

        $request_url = $url .'?'. http_build_query($params);

        // refresh cache
        if (\Input::has('refresh')) {
            $result = Publish::http_post($request_url);
            \Cache::put('api/' . $model . '/' . md5($request_url), $result, 5);
            return $result;
        }

        // cache the request
        return \Cache::remember('api/' . $model . '/' . md5($request_url), 5, function () use ($request_url) {
            return Publish::http_post($request_url);
        });
    }

    public static function http_post($url, $post = null)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);

        if ($post != null) {
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($c);
    }

    public static function get($model, $params)
    {
        $json = self::request($model, $params);
        return json_decode($json);
    }


    public function __construct($id = null, $object = null)
    {
        if (is_null($object)) {
            // get object from API
        }

        $this->id = $id;
        $this->object = $object;
    }

    public function __get($name)
    {
        return isset($this->object->$name) ? $this->object->$name : $this->$name;
    }

    public function __isset($name)
    {
        return isset($this->object->$name);
    }

    public static function find($params = array())
    {
        if (is_numeric($params)) {
            $params = array('id' => $params);
        }

        $class = get_called_class();
        $objects = array();
        $results = self::get(static::$publish_type, $params);

        if (is_object($results)) {
            //return array(new $class($results->id, $results));
            return new $class($results->id, $results);
        }

        foreach ($results as $object) {
            $objects[] = new $class($object->id, $object);
        }

        return $objects;
    }

    public static function hydrate($data)
    {
        $class = get_called_class();
        $objects = array();

        if (is_object($data)) {
            return new $class($data->id, isset($data->object) ? $data->object : $data);
        }

        foreach ($data as $object) {
            $objects[] = new $class($object->id, isset($object->object) ? $object->object : $object);
        }

        return $objects;
    }
}
