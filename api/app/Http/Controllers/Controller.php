<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function jsonResponseFormatter(&$data, &$metaData, $retCode=200){
        $output = (object)[
            'metaData' => config('app.metaDataOutput', false)?$metaData:'',
            'data' => $data,
        ];

        return response()->json($output, $retCode);
    }
}
