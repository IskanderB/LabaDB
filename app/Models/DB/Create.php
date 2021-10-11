<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DB;
use Illuminate\Support\Facades\Storage;


class Create extends DB
{
    use HasFactory;

    public function create(array $columns):void {
        $this->makeDir();
        $this->makeFiles($columns);
    }

    private function makeDir():void {
        Storage::makeDirectory('public/' . $this->name);
    }

    private function makeFiles(array $columns):void {
        unset($columns['directory']);
        foreach ($columns as $key => $value){
            Storage::put('public/' . $this->name . '/' . $value . '.json', '');
        }
        Storage::put('public/' . $this->name . '/columns.json', json_encode($columns));
        Storage::put('public/' . $this->name . '/' . $this->name . '.json', '');
    }


}
