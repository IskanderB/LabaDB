<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Models\Rows\Get;
use Illuminate\Http\Request;

class UniqueColumnsListController extends Controller
{
    public function list(Request $request){
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $get = new Get($request->name);
        $columns = $get->getUniqueColumns();

        return ResponseController::sendResponse(
            json_encode($columns),
            "Unique columns list."
        );
    }
}
