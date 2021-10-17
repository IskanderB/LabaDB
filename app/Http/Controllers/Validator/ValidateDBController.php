<?php

namespace App\Http\Controllers\Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ValidateDBController extends ValidateController
{
    public static function validateDBName(Request $request, bool $exists):mixed {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
        if ($e = self::checkExistsDB($request->name, $exists)) return $e;
        return 'сompleted';
    }

    public static function validateColumnsRequest(Request $request, array $params):mixed {
        $validator = Validator::make($request->all(), $params);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
//        if ($e = self::checkNotEmpty($request, $param)) return $e;
//        $array = $request->$param;
//        if ($e = self::checkType($array, 'array')) return $e;
//        if ($e = self::checkRequiredItems($array, ['name', 'type'])) return $e;
//        if ($e = self::checkType($array['name'], 'string')) return $e;
//        if ($e = self::checkType($array['type'], 'string')) return $e;
//        if ($e = self::checkSizeString($array['name'], 255)) return $e;
//        if ($e = self::checkTypesRange($array['type'])) return $e;
        return 'сompleted';
    }

    public static function validateColumnsFuild(Request $request, string $param):mixed {
        $validator = Validator::make($request->$param, [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
        ]);
    }

    public static function checkDuplicatedColumns($columns) {
        $names = [];
        foreach ($columns as $column) {
            $names[] = $column['name'];
        }
        if(count(array_unique($names))<count($names))
        {
            return "Column names should be unique in your database!";
        }
        else
            return 'сompleted';
    }

    public static function checkExistsDB(string $name, bool $exists){
        $filepath = 'public/config/list.json';
        if (Storage::exists($filepath)) {
            $list = json_decode(Storage::get($filepath), true);
            if (in_array($name, $list) != $exists) {
                if ($exists) {
                    $error = "This database not exists!";
                } else {
                    $error = "This database already exists!";
                }
                return ['name' => $error];
            }
        }
    }

    public static function checkRequiredItems(array $array, array $required) {
        foreach ($required as $item) {
            if (!array_key_exists($item, $array) or !$array[$item])
                return "The " . json_encode($array) . " contains no item $item or it's empty!";

        }
    }

    public static function checkTypesRange(string $type) {
        $allowed_types = [
            'integer',
            'string',
            'double',
            'boolean'
        ];
        foreach ($allowed_types as $allowed_type) {
            if ($type == $allowed_type) return null;
        }
        return "Type $type is out of allowed range!";
    }
}
