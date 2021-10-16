<?php

namespace App\Http\Controllers\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValidateController extends Controller
{
    public static function checkNotEmpty(Request $request, string $param) {
        if ($request->$param == null) {
            return "Parameter $param is empty!";
        }
    }

    public static function checkType(mixed $value, string $type) {
        if (gettype($value) != $type) {
            return "Invalid type! Type should be a $type!";
        }
    }

    public static function checkSizeString(string $value, int $size) {
        if (strlen($value) > $size) {
            return "The $value size exceeds the allowed number of $size characters!";
        }
    }

}
