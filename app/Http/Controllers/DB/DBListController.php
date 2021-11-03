<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use App\Models\Rows\Get;

class DBListController extends Controller
{
    public function list() {
        $list = Get::getDBs();
        return ResponseController::sendResponse(
            $list,
            "Databases list."
        );
    }
}
