<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Rows\Get;

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
        $get = new Get($this->name);
        unset($columns['directory']);
        Storage::put($get->getFilePath('columns'), json_encode($columns));
    }


}
