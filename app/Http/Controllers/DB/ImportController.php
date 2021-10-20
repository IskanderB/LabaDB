<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\ResponseController;
use App\Models\DB\Import;

class ImportController extends Controller
{
    public function import(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateDBController::checkEmptyDB($request->name);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Import($request->name);
        $DB->import();
        $importFilePath = $request->getHost() . $DB->getImportFilePath();
        return ResponseController::sendResponse(
            ['importFilePath' => $importFilePath],
            "Database $request->name was imported successfully!"
        );
    }
}
