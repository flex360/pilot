<?php

namespace Flex360\Pilot\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as DefaultBaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends DefaultBaseController
{
    use DispatchesJobs, ValidatesRequests;
}
