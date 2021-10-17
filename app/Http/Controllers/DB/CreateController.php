<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\ResponseController;
use App\Models\DB\Create;

class CreateController extends Controller
{
    public function create(Request $request) {
        $request->name = $request->name . '_db';
        $result = ValidateDBController::validateDBName($request, false);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);
        $params = [
            'column_1' => ['required', 'array', 'max:2', 'min:2'],
            'column_2' => ['required', 'array', 'max:2', 'min:2'],
            'column_3' => ['required', 'array', 'max:2', 'min:2'],
            'column_4' => ['required', 'array', 'max:2', 'min:2'],
        ];
        $result = ValidateDBController::validateColumnsRequest($request, $params);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        foreach ($params as $param) {
            $result = ValidateDBController::validateColumnsRequest($request, $param);
            if ($result != 'сompleted')
                return ResponseController::sendError($result);
        }
        $columns = [];
        foreach ($params as $param) {
            $columns[$param] = $request->$param;
        }
        $result = ValidateDBController::checkDuplicatedColumns($columns);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Create($request->name);
        $DB->create($columns);
        return ResponseController::sendResponse(
            ['DB_name' => $request->name],
            "Data base $request->name created successfully!"
        );
    }
}
