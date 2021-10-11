<?php

namespace App\Http\Controllers\TestBL;

use App\Http\Controllers\Controller;
use App\Models\DB\Create;
use App\Models\DB\Remove;
use App\Models\DB\Clear;
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
}
