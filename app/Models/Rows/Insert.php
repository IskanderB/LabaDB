<?php

namespace App\Models\Rows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Get;

class Insert extends DB
{
    use HasFactory;

    public function insert(array $data):void {
        $id = $this->inBackup($data);
        $this->inColumns($id, $data);
    }

    private function inBackup(array $data):int {
        $get = new Get($this->name);
        $filepath_global = Storage::path($get->getFilePath($this->name));
        $filepath_local = $get->getFilePath($this->name);
        $id = $get->getLastId($filepath_global) + 1;
        $json = json_encode([
            'id' => $id,
            'data' => $data
        ]);
        Storage::append($filepath_local, $json);
        return $id;
    }

    private function inColumns(int $id, array $data):void {
        foreach ($data as $column => $value) {
            $this->inColumn($id, $column, $value);
        }
    }

    private function inColumn(int $id, string $column, mixed $value):void {
        $get = new Get($this->name);
        $json = json_encode([
            'id' => $id,
            $column => $value
        ]);
        Storage::append($get->getFilePath($column), $json);
    }


}
