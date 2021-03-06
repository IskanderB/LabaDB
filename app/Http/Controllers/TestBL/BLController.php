<?php

namespace App\Http\Controllers\TestBL;

use App\Http\Controllers\Controller;
use App\Models\DB\Create;
use App\Models\DB\Remove;
use App\Models\DB\Clear;
use App\Models\DB\Backup;
use App\Models\DB\Import;
use App\Models\Rows\Insert;
use App\Models\Rows\Select;
use App\Models\Rows\Delete;
use App\Models\Rows\Edit;
use Illuminate\Http\Request;

class BLController extends Controller
{

    public function makedir(Request $request){
        $DB = new Create($request->directory);
        $file_names = $request->all();
        unset($file_names['_token']);
        $DB->create($file_names);
    }

    public function rmdir(Request $request) {
        $DB = new Remove($request->directory);
        $DB->rmDir();
    }

    public function cleardir(Request $request) {
        $DB = new Clear($request->directory);
        $DB->clearDir();
    }

    public function insert(Request $request) {
//        $data = [
//            'id(int)' => 4,
//            'name(str)' => 'Coco',
//            'weight(float)' => 83.4,
//            'checked(bool)' => 0,
//        ];
        $data = [
            'a' => 4,
            'b' => 'Coco',
            'c' => 83.4,
            'd' => 0,
        ];
        $directory = 'test';
        $DB = new Insert($directory);
        $DB->insert($data);
    }

    public function select(Request $request) {
        $DB = new Select($request->directory);
        $rows = $DB->select($request->column, $request->value);
        dd($rows);
    }

    public function delete(Request $request) {
        $DB = new Delete($request->directory);
        $rows = $DB->remove($request->column, $request->value);
        dd($rows);
    }

    public function edit(Request $request) {
        $DB = new Edit($request->directory);
        $data = $request->all();
        unset($data['id_DB_PRIMARY']);
        unset($data['directory']);
        $DB->edit(
            $request->id_DB_PRIMARY,
            $data
        );
    }

    public function getBackup(Request $request) {
        $DB = new Backup($request->directory);
        $DB->getBackupFile();
    }

    public function createbackup(Request $request) {
        $DB = new Backup($request->directory);
        $DB->createBackupFile();
    }

    public function restore(Request $request) {
        $DB = new Backup($request->directory);
        $DB->restore($request);
    }

    public function uplbackup(Request $request) {
        $DB = new Backup($request->directory);
        $DB->uploadBackupFile($request);
    }

    public function import(Request $request) {
        $DB = new Import($request->directory);
        $DB->import();
    }
}
