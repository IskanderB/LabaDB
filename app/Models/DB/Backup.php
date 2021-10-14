<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Get;

class Backup extends DB
{
    use HasFactory;

    public function getBackupFile():string {
        return 'storage/' . $this->name . '/' . $this->name . '.json';
    }
}
