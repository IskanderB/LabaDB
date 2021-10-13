<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Get;

class Clear extends DB
{
    use HasFactory;

    public function clearDir() {
        $get = new Get($this->name);
        $columns = $get->getColumns();
        $links = [];
        foreach ($columns as $value){
            array_push($links, $get->getFilePath($value));
        }
        array_push($links, $get->getFilePath($this->name));
        Storage::delete($links);
    }
}
