<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\ImportHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class TestController extends Controller
{
    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        phpinfo();
        //ImportHelper::parseXML('48561_IMMONET__20190331133824');
    }

}
