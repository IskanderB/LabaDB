<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DB extends Model
{
    use HasFactory;

    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    protected function getColumns():array {
        return array_values(json_decode(Storage::get($this->name . '/columns.json'), true));
    }
}
