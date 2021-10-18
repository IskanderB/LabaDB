<?php

namespace App\Http\Controllers\Rows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\Validator\ValidateRowController;
use App\Http\Controllers\ResponseController;
use App\Models\Rows\Insert;
use App\Models\Rows\Get;

class InsertController extends Controller
{
    public function insert(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateRowController::validateColumns($request);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Insert($request->name);
        $get = new Get($request->name);
        $columns = $get->getColumns();
        $data = [];
        foreach ($columns as $column) {
            $data[$column] = $request->$column;
        }
        $DB->insert($data);
        return ResponseController::sendResponse(
            [
                'DB_name' => $request->name,
                'Added_row' => json_encode($data)
            ],
            "New row successfully added in data base $request->name!"
        );
    }
}
