<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\Validator\ValidateRowController;
use App\Http\Controllers\ResponseController;
use App\Models\Rows\Delete;

class DeleteController extends Controller
{
    public function delete(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateDBController::checkEmptyDB($request->name);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateRowController::validateSearch($request);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Delete($request->name);
        $countDeletedRows = $DB->remove($request->data['name'], $request->data['value'] ?? null);
        if ($countDeletedRows)
            return ResponseController::sendResponse(
                ['countDeletedRows' => $countDeletedRows],
                "$countDeletedRows rows were deleted from $request->name database!"
            );
        else
            return ResponseController::sendError(
                "No rows were deleted from $request->name database!"
            );
    }
}
