<?php
namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Helpers\AddressHelper;
use App\Model\Geo\District;

class SitemapController extends Controller
{

    public function index()
    {
       return response()->view('general.sitemap.index')->header('Content-Type', 'text/xml');
    }

    public function districts()
    {
       $list = District::with(['city', 'city.state', 'city.country'])->get(); 
       $list = AddressHelper::handleLocationList('district', $list);
       return response()->view('general.sitemap.districts', ['list' => $list])->header('Content-Type', 'text/xml');
    }

}
