<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use App\Http\Resources\MealResource;
use Illuminate\Support\Facades\DB;

use App\Meal;
use Hash;

class MealControllerAPI extends Controller
{
    public static function getMealCountPerTable($table_number) {
        return DB::table('meals')->where('table_number', $table_number)->count();
    }
}
