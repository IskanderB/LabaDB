<?php

namespace App\Models\Rows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;

class Get extends DB
{
    use HasFactory;

    public function getRows(array $IDs):array {
        $ROWs = [];
        foreach ($IDs as $id) {
            $ROWs[] = $this->getRow($id);
        }
        return $ROWs;
    }

    public function getRow(int $id):array {
        $filepath = Storage::path($this->getFilePath($this->name));
        $handle = @fopen($filepath, "r");
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $buffer = json_decode($json, true);
                if ($buffer and $buffer['id'] == $id)
                    break;
            }
            fclose($handle);
        }
        return $buffer;
    }

    public function getIDs(string $column, mixed $value):array {
        $filepath = Storage::path($this->getFilePath($column));
        $handle = @fopen($filepath, "r");
        $IDs = [];
        if ($handle) {
            while (($json = fgets($handle, 4096)) !== false) {
                $buffer = json_decode($json, true);
                if ($buffer and $buffer[$column] == $value)
                    $IDs[] = $buffer['id'];
            }
            fclose($handle);
        }
        return $IDs;
    }

    public function getColumns():array {
        $columns_file = json_decode(Storage::get($this->getFilePath('config/columns')), true);
        $columns = [];
        foreach ($columns_file as $item) {
            $columns[] = $item['name'];
        }
        return $columns;
    }

    public function getColumnsAndTypes():array {
        return json_decode(Storage::get($this->getFilePath('config/columns')), true);
    }

    public function getLastId($filepath):int {
//        $lastRow = json_decode($this->tailCustom($filepath), true);
//        if ($lastRow)
//            return intval($lastRow['id']);
//        else
//            return 0;
        return intval(Storage::get($this->getFilePath('config/lastId')));
    }

    public function tailCustom($filepath, $lines = 1, $adaptive = true) {

        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {

            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");

        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {

            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);

        }

        // Close file and return
        fclose($f);
        return trim($output);

    }

    public function getFilePath(string $fileName):string {
        return 'public/' . $this->name . '/' . $fileName . '.json';
    }
}
