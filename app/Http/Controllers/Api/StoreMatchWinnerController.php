<?php

namespace App\Http\Controllers\Api;

use App\Match;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StoreMatchWinnerController extends Controller
{
    public function __invoke(Request $request)
    {
        DB::beginTransaction();

        try {

            $match = Match::create(['winner' => $request->winner]);

            DB::commit();

            return $match;

        } catch (Exception $e) {

            DB::rollBack();


            return response()->json(['messsage' => 'Unable to store winner']);


        }
    }
}
