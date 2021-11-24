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
        $this->addNameInList();
    }

    private function makeDir():void {
        Storage::makeDirectory('public/' . $this->name);
        Storage::makeDirectory('public/' . $this->name . '/config');
    }

    private function makeFiles(array $columns):void {
        $get = new Get($this->name);
        Storage::put($get->getFilePath('config/columns'), json_encode($columns));
        Storage::put($get->getFilePath('config/lastId'), json_encode(['id' => 0, 'count' => 0]));
    }

    private function addNameInList() {
        $filepath = 'public/config/list.json';
        if (!Storage::exists($filepath)) {
            Storage::put($filepath, json_encode([$this->name]));
        }
        else {
            $list = json_decode(Storage::get($filepath), true);
            $list[] = $this->name;
            Storage::put($filepath, json_encode($list));
        }
    }
}
