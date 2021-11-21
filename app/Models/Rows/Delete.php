<?php

namespace App\Models\Rows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Get;

class Delete extends DB
{
    use HasFactory;

    public function remove(string $column, mixed $value):int {
        $get = new Get($this->name);
        $IDs = $get->getRows($column, $value, 'IDs');
        $deletedRows = $this->removeRows($IDs, $this->name);
        $columns = $get->getColumns();
        foreach ($columns as $item) {
            $this->removeRows($IDs, $item);
        }
        return count($IDs);
    }

    private function removeRows(array $IDs, string $fileName) {
        $get = new Get($this->name);
        $filepath_local = $get->getFilePath($fileName);
        $filepath = Storage::path($filepath_local);
        $filepath_new_local = $get->getFilePath($fileName . '_new');
        $handle = @fopen($filepath, "r");
        $deletedRows = [];
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $buffer = json_decode($json, true);
                $add = true;
                foreach ($IDs as $id) {
                    if ($buffer and $buffer['id'] == $id){
                        $add = false;
                        $deletedRows[] = $buffer;
                    }
                }
                if ($add)
                    Storage::append($filepath_new_local, json_encode($buffer));
            }
            fclose($handle);
        }
        Storage::delete($filepath_local);
        if (Storage::exists($filepath_new_local))
            Storage::move($filepath_new_local, $filepath_local);
        if ($fileName == $this->name)
            return $deletedRows;
    }
}
