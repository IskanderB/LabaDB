<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\ResponseController;
use App\Models\DB\Remove;

class RemoveController extends Controller
{
    public function remove(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);
        $DB = new Remove($request->name);
        $DB->remove();
        return ResponseController::sendResponse(
            ['Removed_DB_name' => $request->name],
            "Data base $request->name removed successfully!"
        );
    }
}
