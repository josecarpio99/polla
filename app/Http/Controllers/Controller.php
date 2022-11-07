<?php

namespace App\Http\Controllers;

use App\Libraries\Core;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public $core;

    public function __construct()
    {
        /** define Core as global Library */
        $this->core = new Core();
    }

    public function missingMethod()
    {
        return $this->core->setResponse();
    }
}
