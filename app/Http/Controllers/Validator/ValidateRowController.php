<?php

namespace App\Http\Controllers\Validator;

use App\Http\Controllers\Validator\ValidateController;
use Illuminate\Http\Request;
use App\Models\Rows\Get;

class ValidateRowController extends ValidateController
{
    public static function validateColumns(Request $request) {
        $get = new Get($request->name);
        if ($e = self::checkMatchColumns($request, $get)) return $e;
    }

    public static function checkMatchColumns(Request $request, Get $get) {
        $columns = $get->getColumns();
        foreach ($columns as $column) {
            if (!$request->$column)
                return "Column $column is not exists in your request or empty!";
        }
    }

    public static function checkTypeColumns(Request $request, Get $get) {
        $columns_t = $get->getColumnsAndTypes();
    }
}
