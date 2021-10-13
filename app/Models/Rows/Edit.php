<?php

namespace App\Models\Rows;

use App\Models\Rows\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;

class Edit extends DB
{
    use HasFactory;

    public function edit(int $id, array $data):void {
        $this->editRow($id, 'data', $data);
        foreach ($data as $fileName => $value) {
            $this->editRow($id, $fileName, $value);
        }
    }

    private function editRow(int $id, string $fileName, mixed $value):void {
        $get = new Get($this->name);
        if ($fileName == 'data')
            $filepath_local = $get->getFilePath($this->name);
        else
            $filepath_local = $get->getFilePath($fileName);
        $filepath_global = Storage::path($filepath_local);
        $filepath_new_local = $get->getFilePath($fileName . '_new');
        $handle = @fopen($filepath_global, "r");
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $buffer = json_decode($json, true);
                if ($buffer and $buffer['id'] == $id){
                    $buffer[$fileName] = $value;
                }
                Storage::append($filepath_new_local, json_encode($buffer));
            }
            fclose($handle);
        }
        Storage::delete($filepath_local);
        if (Storage::exists($filepath_new_local))
            Storage::move($filepath_new_local, $filepath_local);
    }
}
