<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DB;
use Illuminate\Support\Facades\Storage;

class Remove extends DB
{
    use HasFactory;

    public function rmDir():void {
        Storage::deleteDirectory('public/' . $this->name);
    }
}
