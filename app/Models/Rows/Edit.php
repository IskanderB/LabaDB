<?php

namespace App\Models\Rows;

use App\Models\Rows\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;
use App\Models\Rows\Delete;
use App\Models\Rows\Insert;

class Edit extends DB
{
    use HasFactory;

    public function edit(int $id, array $data):void {
        $Delete = new Delete($this->name);
        $Delete->remove('', '', [$id]);
        $Insert = new Insert($this->name);
        $Insert->insert($data);
    }
}
