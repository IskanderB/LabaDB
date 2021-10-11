<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\DB;

class Clear extends DB
{
    use HasFactory;

    public function clearDir() {
        $columns = $this->getColumns();
        $links = [];
        foreach ($columns as $value){
            array_push($links, 'public/' . $this->name . '/' . $value . '.json');
        }
        array_push($links, 'public/' . $this->name . '/' . $this->name . '.json');
        Storage::delete($links);
    }
}
