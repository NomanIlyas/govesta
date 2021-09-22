<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Geo\Country;
use Illuminate\Http\Request;
use Validator;

class CountryController extends Controller
{
    /**
     * States
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::all();
        return view('admin/country/index', compact('countries'));
    }

    /**
     * Status
     *
     * @return \Illuminate\Http\Response
     */
    public function status($id, $status)
    {
        $country = Country::find($id);
        $country->status = $status;
        $country->save();
        return redirect('/admin/country/list');
    }
}
