<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\ResponseController;
use App\Models\DB\Clear;

class ClearController extends Controller
{
    public function clear(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Clear($request->name);
        $DB->clear();
        return ResponseController::sendResponse(
            ['DB_name' => $request->name],
            "Database $request->name is clear!"
        );
    }
}
