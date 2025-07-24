<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;  // ← autoriza()
use Illuminate\Foundation\Validation\ValidatesRequests;    // ← valida()

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
