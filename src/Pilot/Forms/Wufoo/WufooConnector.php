<?php

namespace Flex360\Pilot\Pilot\Forms\Wufoo;

class WufooConnector
{

    public $hash = 'z7w8s5';
    private $api_key = 'MOB1-QI72-SYOM-IMCZ';
    private $subdomain = 'flex360dev';
    public $debug = false;

    public static $types = [
        'id' => 'int(10) unsigned NOT NULL',
        'text' => 'VARCHAR (255)',
        'textarea' => 'VARCHAR (255)',
        'select' => 'VARCHAR (100)',
        'phone' => 'VARCHAR (10)',
        'email' => 'VARCHAR (100)',
        'date' => 'DATETIME',
        'checkbox' => 'TEXT',
        'radio' => 'VARCHAR (100)',
        'shortname' => 'VARCHAR (100)',
        'number' => 'VARCHAR (100)',
        'file' => 'VARCHAR(255)',
        'address' => [
            'address_line_1' => 'VARCHAR (100)',
            'address_line_2' => 'VARCHAR (100)',
            'city' => 'VARCHAR (100)',
            'state_province_region' => 'VARCHAR (100)',
            'postal_zip_code' => 'VARCHAR (100)',
            'country' => 'VARCHAR (100)',
        ],
    ];

    public function __construct($params = [])
    {
        foreach ($params as $k => $v) {
            $this->$k = $v;
        }
    }

    public static function make($params = [])
    {
        return new WufooConnector($params);
    }

    public function getEntries($format = 'json', $page = 0)
    {
        $page_size = 100;
        $entries = array();

        $url = 'https://' . $this->subdomain . '.wufoo.com/api/v3/forms/' . $this->hash . '/entries/count.' . $format;
        $cnt = $this->curlit($url);
        $cnt = json_decode($cnt);

        $entry_count = $cnt->EntryCount;
        $page_count_max = ceil($entry_count / $page_size);
        $page_count = $page + 10;
        $page_count = $page_count_max < $page_count ? $page_count_max : $page_count;
        $next_page = $page_count == $page_count_max ? null : $page_count+1;

        $url = 'https://' . $this->subdomain . '.wufoo.com/api/v3/forms/' . $this->hash . '/entries.' . $format .
        '?pageStart=%s&pageSize=' . $page_size;

        for ($page; $page <= $page_count; $page++) {
            $json = $this->curlit(sprintf($url, $page*$page_size));
            $entries = array_merge($entries, json_decode($json)->Entries);
        }

        $response = compact('entries', 'next_page');

        return $response;
    }

    public function getFields($format = 'json')
    {
        $url = 'https://' . $this->subdomain . '.wufoo.com/api/v3/forms/' . $this->hash . '/fields.' . $format;

        $response = $this->curlit($url);

        $fields = json_decode($response);

        return $fields->Fields;
    }

    private function curlit($url)
    {
        $curl = curl_init($url);       //1
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);                          //2
        curl_setopt($curl, CURLOPT_USERPWD, $this->api_key . ':footastic');   //3
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                     //4
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Wufoo Sample Code');             //5

        $response = curl_exec($curl);                                           //6
        $resultStatus = curl_getinfo($curl);                                    //7

        if ($resultStatus['http_code'] == 200) {                     //8
            if ($this->debug) {
                echo '<pre>'.print_r(json_decode($response), true).'</pre>';
            }
                return $response;
        } else {
            echo 'Call Failed '.print_r($resultStatus);                         //9
            return null;
        }
    }
}
