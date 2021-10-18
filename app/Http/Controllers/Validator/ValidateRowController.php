<?php

namespace App\Http\Controllers\Validator;

use App\Http\Controllers\Validator\ValidateController;
use Illuminate\Http\Request;
use App\Models\Rows\Get;
use Illuminate\Support\Facades\Validator;
use App\Models\Rows\Select;

class ValidateRowController extends ValidateController
{
    public static function validateColumns(Request $request) {
        $get = new Get($request->name);
        if ($e = self::checkMatchColumns($request, $get)) return $e;
        if ($e = self::checkUniqueColumns($request, $get)) return $e;
        return 'сompleted';
    }

    public static function checkMatchColumns(Request $request, Get $get) {
        $columnsAndTypes = $get->getColumnsAndTypes();
        $rules = [];
        foreach ($columnsAndTypes as $column => $type) {
            if($type == 'string')
                $rules[$column] =['required', $type, 'max:255'];
            else
                $rules[$column] =['required', $type];
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
    }

    public static function checkUniqueColumns(Request $request, Get $get) {
        $columns_uniq = $get->getUniqueColumns();
        $DB = new Select($request->name);
        foreach ($columns_uniq as $column) {
            $value = $request->$column;
            if ($DB->select($column, $value))
                return [$column => "Row with $value in $column already exists!"];

        }
    }
}
