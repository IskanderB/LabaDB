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
        $result = ValidateDBController::validateDBName($request, false);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);
        $request->name = $request->name . '_db';

        $params = [
            'column_1',
            'column_2',
            'column_3',
            'column_4',
        ];

        foreach ($params as $param) {
            $result = ValidateDBController::validateColumnsRequest($request, $param);
            if ($result != 'сompleted')
                return ResponseController::sendError($result);
        }

        $columns = [];
        foreach ($params as $param) {
            $columns[$param] = $request->$param;
        }
        $DB = new Create($request->name);
        $DB->create($columns);

    }
}
