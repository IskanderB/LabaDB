<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\Validator\ValidateRowController;
use App\Http\Controllers\ResponseController;

class InsertController extends Controller
{
    public function insert(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateRowController::validateColumns($request);
    }
}
