<?php

namespace App\Models\DB;

use App\Models\Rows\Insert;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\DB\Clear;
use App\Models\Rows\Get;

class Backup extends DB
{
    use HasFactory;

    public function createBackupFile() {
        $get = new Get($this->name);
        Storage::deleteDirectory('public/' . $this->name . '/backup');
        Storage::copy($get->getFilePath($this->name), $get->getFilePath('backup/' . $this->name));
    }

    public function getBackupFile():string {
        return 'storage/' . $this->name . 'backup/' . $this->name . '.json';
    }

    public function uploadBackupFile(Request $request) {
        $get = new Get($this->name);
        Storage::deleteDirectory('public/' . $this->name . '/backup');
        $path = $request->file('file')->store($this->name . '/backup', 'public');
        Storage::move('public/' . $path, $get->getFilePath('backup/' . $this->name));
    }

    public function restore():void {
        $get = new Get($this->name);
        $this->restoreBasicFile($get);

//        $this->createColumns($get);
    }

    private function restoreBasicFile(Get $get):void {
        $filepath_backup = $get->getFilePath('backup/' . $this->name);
        if (Storage::exists($filepath_backup)){
            $Clear = new Clear($this->name);
            $Clear->clear();
        }
        $handle = @fopen(Storage::path($filepath_backup), "r");
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $buffer = json_decode($json, true);
                $data = $buffer['data'];
                $Insert = new Insert($this->name);
                $Insert->insert($data);
            }
            fclose($handle);
        }
//            Storage::copy($get->getFilePath('backup/' . $this->name), $get->getFilePath($this->name));
    }

    private function createColumns(Get $get):void {
        $filepath = Storage::path($get->getFilePath($this->name));
        $columns = $get->getColumns();
        $filepath_columns = [];
        foreach ($columns as $column) {
            $filepath_columns[$column] = $get->getFilePath($column);
        }
        $handle = @fopen($filepath, "r");
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $buffer = json_decode($json, true);
                foreach ($buffer['data'] as $column => $value) {
                    Storage::append($filepath_columns[$column], json_encode(['id' => $buffer['id'], $column => $value]));
                }
            }
            fclose($handle);
        }
    }
}
