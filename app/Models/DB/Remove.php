<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DB;
use Illuminate\Support\Facades\Storage;

class Remove extends DB
{
    use HasFactory;

    public function rmDir():void {
        Storage::deleteDirectory('public/' . $this->name);
        $this->rmNameFromList();
    }

    private function rmNameFromList():void {
        $filepath = 'public/config/list.json';
        $list = json_decode(Storage::get($filepath), true);
        foreach ($list as $key => $value) {
            if ($value == $this->name){
                $rmKey = $key;
                break;
            }
        }
        unset($list[$rmKey]);
        Storage::put($filepath, json_encode($list));
    }
}
