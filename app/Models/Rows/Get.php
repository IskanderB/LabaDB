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
        $filepath = Storage::path($this->getFilePath($this->name));
        if (!file_exists($filepath)){
            return [];
        }
        $file = new \SplFileObject($filepath);
        foreach ($IDs as $id) {
            $ROWs[] = $this->getRow($id, $file);
        }
        return $ROWs;
    }

    public function getRow($value, $file):array {
        $column = 'id';
        $begin = 0;
        $end = $file->fstat()['size'];
        if (!$file->eof() and $end) {
            while (true) {
                $median = floor(($begin + $end)/2);
                $file->fseek($median);
                $json = $file->current();
                $str = json_decode($json, true);
                if ($str == null) {
                    $file->next();
                    $json = $file->current();
                    $str = json_decode($json, true);
                }
                $row = $str[$column] ?? null;
                if (strnatcmp($row, $value) == 0) {
                    $result = $str ;
                    return $result;
                }
                elseif (strnatcmp($row, $value) == 1) {
                    if ($begin == $end or $median == $begin) {
                        return [];
                    }
                    $end = $median - 1;
                    continue;
                }
                else {
                    $file->rewind();
                    $json = $file->current();
                    $str = json_decode($json, true);
                    $row = $str[$column] ?? null;
                    if (strnatcmp($row, $value) == 0) {
                        $result = $str;
                        return $result;
                    }
                    if ($begin == $end or $median == $end) {
                        return [];
                    }
                    $begin = $median + 1;
                    continue;
                }
            }
        }
        else {
            return [];
        }
    }

    public function getIDs(string $column, mixed $value):array {
        if (!file_exists(Storage::path($this->getFilePath($column)))){
            return [];
        }
        $begin = 0;
        $file = new \SplFileObject(Storage::path($this->getFilePath($column)));
        $end = $file->fstat()['size'];
        if (!$file->eof() and $end) {
            while (true) {
                $median = floor(($begin + $end)/2);
                $file->fseek($median);
                $json = $file->current();
                $str = json_decode($json, true);
                if ($str == null) {
                    $file->next();
                    $json = $file->current();
                    $str = json_decode($json, true);
                }
                $row = $str['data'][$column] ?? null;
                if (strnatcmp($row, $value) == 0) {
                    $result = self::readRows($file->ftell(), 2, $value, $column, $file);
                    return $result;
                }
                elseif (strnatcmp($row, $value) == 1) {
                    if ($begin == $end or $median == $begin) {
                        return [];
                    }
                    $end = $median - 1;
                    continue;
                }
                else {
                    $file->rewind();
                    $json = $file->current();
                    $str = json_decode($json, true);
                    $row = $str['data'][$column] ?? null;
                    if (strnatcmp($row, $value) == 0) {
                        $result = self::readRows($file->ftell(), 2, $value, $column, $file);
                        return $result;
                    }
                    if ($begin == $end or $median == $end) {
                        return [];
                    }
                    $begin = $median + 1;
                    continue;
                }
            }
        }
        else {
            return [];
        }
    }

    public static function readRows ($position, $number, $item, $column, $file) {
        $newPosition = $position - 4096*$number;
        if ($newPosition < 0)
            $newPosition = 0;
        $file->fseek($newPosition);
        $json = $file->current();
        $str = json_decode($json, true);
        if ($str == null) {
            $file->next();
            $json = $file->current();
            $str = json_decode($json, true);
        }
        $row = $str['data'][$column];
        if (strnatcmp($row, $item) == 0 and $newPosition) {
            self::readRows($newPosition, $number, $item, $column, $file);
        }
        $i = 0;
        while (true) {
            if ($newPosition  or $i)
                $file->next();
            $i++;
            $json = $file->current();
            $str = json_decode($json, true);
            $row = $str['data'][$column] ?? null;
            if ($row != null and strnatcmp($row, $item) == 0) {
                break;
            }
        }
        $i = 0;
        while (true) {
            if ($newPosition or $i){
                $file->next();
            }
            $i++;
            $json = $file->current();
            $str = json_decode($json, true);
            $row = $str['data'][$column] ?? null;
            if ($row != null and strnatcmp($row, $item) == 0) {
                $result[] = $str['id'];
            }
            else {
                break;
            }
        }
        return $result;
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
        $columnsFromJson = json_decode(Storage::get($this->getFilePath('config/columns')), true);
        $columnsAndTypes = [];
        foreach ($columnsFromJson as $item) {
            $columnsAndTypes[$item['name']] = $item['type'];
        }
        return $columnsAndTypes;
    }

    public function getLastId($filepath):int {
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

    public function getUniqueColumns():array {
        $columnsFromJson = json_decode(Storage::get($this->getFilePath('config/columns')), true);
        $uniques = [];
        foreach ($columnsFromJson as $key => $item) {
            if ($item['unique'])
                $uniques[] = $item['name'];
        }
        return $uniques;
    }

    public function getAllFromColumns():array {
        return json_decode(Storage::get($this->getFilePath('config/columns')), true);
    }

    public static function getDBs():string {
        $filepath = 'public/config/list.json';
        if (Storage::exists($filepath))
            return Storage::get($filepath);
        else
            return '';
    }
}
