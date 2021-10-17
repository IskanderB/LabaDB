<?php

namespace App\Http\Controllers\Validator;

use App\Http\Controllers\Validator\ValidateController;
use Illuminate\Http\Request;
use App\Models\Rows\Get;
use Illuminate\Support\Facades\Validator;

class ValidateRowController extends ValidateController
{
    public static function validateColumns(Request $request) {
        $get = new Get($request->name);
        if ($e = self::checkMatchColumns($request, $get)) return $e;
        if ($e = self::checkTypeColumns($request, $get)) return $e;
        return 'сompleted';
    }

    public static function checkMatchColumns(Request $request, Get $get) {
        $validator = Validator::make($request->all(), [
            'test_name2' => 'required',
        ]);
        dd($validator->validated());
        $columns = $get->getColumns();
        foreach ($columns as $column) {
            if ($request->$column == null)
                return "Column $column is not exists in your request or empty!";
        }
    }

    public static function checkTypeColumns(Request $request, Get $get) {
        $getColumnsAndTypes = $get->getColumnsAndTypes();
        foreach ($getColumnsAndTypes as $column => $type) {
            if (gettype($request->$column) != $type)
                return "Type of column $column should be a $type!";
        }
    }
}
