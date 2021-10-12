<?php

namespace App\Models\Rows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;

class Delete extends DB
{
    use HasFactory;

    public function delete(string $column, mixed $value):void {

    }
}
