<?php

namespace App\Http\Controllers\Api;

use App\Match;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FetchMatchHistoryController extends Controller
{
    public function __invoke()
    {
        $records = Match::get()->groupBy('winner');

        $output = [];

        foreach($records as $winner => $matches)
        {
            $output[$winner] = $matches->count();
        }

        return $output;
    }
}
