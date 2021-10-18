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
        $allFromColumns = $get->getAllFromColumns();
        $rules = [];
        foreach ($allFromColumns as $key => $item) {
            $rules[$item['name']] = [$item['type']];
            if ($item['unique']) {
                $rules[$item['name']][] = 'required';
            }
            if ($item['type'] == 'string') {
                $rules[$item['name']][] = 'max:255';
            }
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

    public static function validateSearch(Request $request) {
        $validator = Validator::make($request->all(), [
            'search_data' => ['required', 'array', 'min:1'],
        ]);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }

        $validator = Validator::make($request->search_data, [
            'name' => ['required', 'string', 'max:255'],
            'value' => ['max:255'],
        ]);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }

        $get = new Get($request->name);
        $columns = $get->getColumns();
        foreach ($columns as $column) {
            if ($request->search_data['name'] == $column)
                return 'сompleted';
        }
        $column_name = $request->search_data['name'];
        $DB_name = $request->name;
        return ["name" => "Column $column_name is not exists in $DB_name data base!"];
    }
}
