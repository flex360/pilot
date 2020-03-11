<?php

namespace Flex360\Pilot\Pilot\Forms\Wufoo;

class WufooInterceptor {

    public $hash;
    public $data = [];
    public $ignore = ['_token', 'saveForm'];
    public $phone = [];

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    public static function make($hash)
    {
        return new self($hash);
    }

    public function loadData($data = [])
    {
        foreach ($this->ignore as $ignore) {
            unset($data[$ignore]);
        }

        foreach ($this->phone as $phone) {
            $data[$phone] = preg_replace('/[^0-9]/', '', $data[$phone]);
        }

        $this->data = $data;

        return $this;
    }

    public function data()
    {
        return collect($this->data);
    }

    public function wufooUrl()
    {
        return 'https://flex360dev.wufoo.com/forms/' . $this->hash . '/';
    }

    public function url()
    {
        return route('wufoo.confirm', $this->hash);
    }

    public function ignore($field)
    {
        array_push($this->ignore, $field);

        return $this;
    }

    public function phone($field)
    {
        array_push($this->phone, $field);

        return $this;
    }

    public function register()
    {
        app()->instance($this->hash, $this);
        
        return $this;
    }

}
