<?php

namespace App\Http\Controllers;

use App\Libraries\Core;
use App\Traits\ApiResponser;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiResponser;

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
