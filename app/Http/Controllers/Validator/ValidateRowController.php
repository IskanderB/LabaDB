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
        if ($e = self::checkDataExists($request)) return $e;
        if ($e = self::checkMatchColumns($request, $get)) return $e;
        $unique = self::checkUniqueRow($request, $get);
        if (!$unique['unique'])
            return $unique['errors'];
        return 'сompleted';
    }

    public static function checkDataExists(Request $request) {
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'array', 'min:1']
        ]);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
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
        $validator = Validator::make($request->data, $rules);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
    }

    public static function checkUniqueRow(Request $request, Get $get):array {
        $column_uniq = $get->getUniqueColumns();
        $DB = new Select($request->name);
        $row = $DB->select($column_uniq[0], $request->data[$column_uniq[0]]);
        $unique = [
            'allUnique' => false,
            'unique' => true,
            'errors' => [],
        ];
        foreach ($column_uniq as $column) {
            $value = $request->data[$column];
            $row = $DB->select($column, $value);
            if ($row) {
                $unique['errors'][$column] = "Column $column should be unique!";
                $unique['unique'] = false;
                foreach ($column_uniq as $column_in) {
                    $value_in = $request->data[$column_in];
                    if($row[0]['data'][$column_in] == $value_in)
                        $unique['allUnique'] = $row[0]['id'];
                }
            }
        }
        return $unique;
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

    public static function validateEdit(Request $request) {
        $get = new Get($request->name);
        if ($e = self::checkDataExists($request)) return $e;
        if ($e = self::checkMatchColumns($request, $get)) return $e;
        $unique = self::checkUniqueRow($request, $get);
        if (!$unique['allUnique'])
            return "No such row exists!";
        else
            return $unique['allUnique'];
    }
}
