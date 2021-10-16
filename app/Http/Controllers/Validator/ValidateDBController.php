<?php

namespace App\Http\Controllers\Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Validator\ValidateController;
use Illuminate\Support\Facades\Storage;

class ValidateDBController extends ValidateController
{
    public static function validateDBName(Request $request, bool $exists):string {
        if ($e = self::checkNotEmpty($request, 'name')) return $e;
        if ($e = self::checkType($request->name, 'string')) return $e;
        if ($e = self::checkSizeString($request->name, 255)) return $e;
        if ($e = self::checkExistsDB($request->name, $exists)) return $e;
        return 'сompleted';
    }

    public static function validateColumnsRequest(Request $request, string $param):string {
        if ($e = self::checkNotEmpty($request, $param)) return $e;
        $array = json_decode($request->$param, true);
        if ($e = self::checkType($array, 'array')) return $e;
        if ($e = self::checkRequiredItems($array, ['name', 'type'])) return $e;
        if ($e = self::checkType($array['name'], 'string')) return $e;
        if ($e = self::checkType($array['type'], 'string')) return $e;
        if ($e = self::checkSizeString($array['name'], 255)) return $e;
        if ($e = self::checkTypesRange($array['type'])) return $e;
        return 'сompleted';
    }

    public static function validateColumnsName($array) {
        if ($e = self::checkNotEmptyInArray($array)) return $e;
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
                return $error;
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
            'int',
            'string',
            'float',
            'bool'
        ];
        foreach ($allowed_types as $allowed_type) {
            if ($type == $allowed_type) return null;
        }
        return "Type $type is out of allowed range!";
    }
}
