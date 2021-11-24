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
        $id = $this->inBasic($data);
        $this->inColumns($id, $data);
    }

    private function inBasic(array $data):int {
        $get = new Get($this->name);
        $filepath_global = Storage::path($get->getFilePath($this->name));
        $filepath_local = $get->getFilePath($this->name);
        $id = $get->getLastId($filepath_global) + 1;
        $json = json_encode([
            'id' => $id,
            'data' => $data
        ]);
        if (Storage::exists($get->getFilePath('config/lastId'))){
            Storage::append($filepath_local, $json);
            $last_j = Storage::get($get->getFilePath('config/lastId'));
            $last = json_decode($last_j, true);
            $last['id'] = $id;
            $last['count']++;
        }
        else {
            $last = [
                'id' => $id,
                'count' => 1
            ];
        }
        Storage::put($get->getFilePath('config/lastId'), json_encode($last));
        return $id;
    }

    private function inColumns(int $id, array $data):void {
        foreach ($data as $column => $value) {
            $this->inColumn($id, $column, $value);
        }
    }

    private function inColumn(int $id, string $column, mixed $value):void {
        $get = new Get($this->name);
        $filepath = Storage::path($get->getFilePath($column));
        $json = json_encode([
            'id' => $id,
            'data' => [
                $column => $value
            ]
        ]);
        if (!file_exists($filepath)){
            Storage::append($get->getFilePath($column), $json . PHP_EOL);
            return;
        }
        $position = self::searchPosition($filepath, $value, $column);
        self::insertOnPosition($filepath, $json . PHP_EOL, $position);
    }

    public static function searchPosition($filepath, $item, $column) {
        $begin = 0;
        $start = microtime(true);
        $i = 0;
        $file = new \SplFileObject($filepath);
        $fsize = $file->fstat()['size'];
        $end = $fsize;
        if (!$file->eof()) {
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
                if (strnatcmp($row, $item) == 0) {
                    $time = microtime(true) - $start;
                    $success = true;
                    $file->next();
                    $result = $file->ftell();
                    break;
                }
                elseif (strnatcmp($row, $item) == 1) {
                    if ($begin == $end or $median == $begin) {
                        break;
                    }
                    $end = $median - 1;
                    continue;
                }
                else {
                    if ($begin == $end or $median == $end) {
                        $pos = $file->ftell();
                        $file->rewind();
                        $json = $file->current();
                        $str = json_decode($json, true);
                        $row = $str['data'][$column] ?? null;
                        if (strnatcmp($row, $item) == 1) {
                            $result = 0;
                            break;
                        }

                        $result = $pos;
                        break;
                    }
                    $result = $file->ftell();
                    $file->next();
                    $json2 = $file->current();
                    $str = json_decode($json2, true);
                    $row = $str['data'][$column] ?? null;
                    if (strnatcmp($row, $item) == 1) {
                        break;
                    }
                    $begin = $median + 1;
                    continue;
                }
            }
        }
        return $result ?? 0;
    }

    public static function insertOnPosition($file, $data, $position) {
        $fpFile = fopen($file, "rw+");
        $fpTemp = fopen('php://temp', "rw+");
        stream_copy_to_stream($fpFile, $fpTemp, -1, $position);
        fseek($fpFile, $position);
        fwrite($fpFile, $data);
        rewind($fpTemp);
        stream_copy_to_stream($fpTemp, $fpFile);

        fclose($fpFile);
        fclose($fpTemp);
    }


}
