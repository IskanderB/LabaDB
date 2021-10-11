<?php

namespace App\Http\Controllers\TestBL;

use App\Http\Controllers\Controller;
use App\Models\DB\Create;
use App\Models\DB\Remove;
use App\Models\DB\Clear;
use App\Models\Rows\Insert;
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
        $data = [
            'id(int)' => 1,
            'name(str)' => 'Dobro',
            'weight(float)' => 82.6,
            'checked(bool)' => 0,
        ];
        $directory = 'test_db';
        $DB = new Insert($directory);
        $DB->insert($data);
    }
}
