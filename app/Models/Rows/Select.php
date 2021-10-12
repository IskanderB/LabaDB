<?php

namespace App\Models\Rows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Get;

class Select extends DB
{
    use HasFactory;

    public function select(string $column, mixed $value):array {
        $get = new Get($this->name);
        return $get->getRows($get->getIDs($column, $value));
    }
}
