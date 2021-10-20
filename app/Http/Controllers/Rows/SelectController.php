<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\Validator\ValidateRowController;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Rows\Select;

class SelectController extends Controller
{
    public function select(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateDBController::checkEmptyDB($request->name);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateRowController::validateSearch($request);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Select($request->name);
        $result = $DB->select(
            $request->data['name'],
            $request->data['value'] ?? null
        );
        if ($result)
            return ResponseController::sendResponse(
                $result,
                "Request returned the following rows"
            );
        else
            return ResponseController::sendError(
                "No results found"
            );

    }
}
