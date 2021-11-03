<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\ResponseController;
use App\Models\Rows\Get;

class ColumnsListController extends Controller
{
    public function list(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $get = new Get($request->name);
        $columns = $get->getColumnsAndTypes();

        return ResponseController::sendResponse(
            json_encode($columns),
            "Columns list."
        );
    }
}
