<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\Validator\ValidateRowController;
use App\Http\Controllers\ResponseController;
use App\Models\Rows\Edit;
use App\Models\Rows\Get;

class EditController extends Controller
{
    public function edit(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $id = ValidateRowController::validateEdit($request);
        if (gettype($id) != 'integer')
            return ResponseController::sendError($result);

        $DB = new Edit($request->name);
        $DB->edit($id, $request->data);
        return ResponseController::sendResponse(
            [
                'DB_name' => $request->name,
                'Added_row' => json_encode($request->data)
            ],
            "The row edited successfully!"
        );
    }
}
