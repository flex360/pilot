<?php

namespace Flex360\Pilot\Http\Controllers;

use Flex360\Pilot\Pilot\Forms\FormHandler;

class FormController extends Controller
{
    public function handler()
    {
        $data = request()->all();

        $handler = new FormHandler($data);

        if ($handler->valid()) {

            return $handler->send();
            
        } else {

            return $handler->getErrorRedirect();

        }
        
    }
}