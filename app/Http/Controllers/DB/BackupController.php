<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateDBController;
use App\Http\Controllers\ResponseController;
use App\Models\DB\Backup;

class BackupController extends Controller
{
    public function createBackupFile(Request $request) {
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateDBController::checkEmptyDB($request->name);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Backup($request->name);
        $DB->createBackupFile();
        return ResponseController::sendResponse(
            ['DB_name' => $request->name],
            "Backup file for database $request->name was created!"
        );
    }

    public function restore(Request $request){
        $result = ValidateDBController::validateDBName($request, true);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $result = ValidateDBController::checkExistsBackup($request->name);
        if ($result != 'сompleted')
            return ResponseController::sendError($result);

        $DB = new Backup($request->name);
        $DB->restore();
        return ResponseController::sendResponse(
            ['DB_name' => $request->name],
            "Database $request->name was restored successfully!"
        );
    }
}
