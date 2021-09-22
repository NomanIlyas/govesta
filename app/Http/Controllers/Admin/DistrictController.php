<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\Geo\District;
use App\Model\Geo\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class DistrictController extends Controller
{

    /**
     * List
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->query('id');
        $districts = District::where('city_id', $id)->with(['city', 'city.country', 'city.state'])->get();
        return view('admin/district/index', compact('districts', 'id'));
    }

    /**
     * Edit
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->query('id');
        $district = District::find($id);
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'featured_image' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $image = $request->featured_image;
            if ($image) {
                if ($district->featured_image_id) {
                    FileHelper::removeFile($district->featured_image_id);
                }
                $uploadedFile = FileHelper::upload('district', $image);
                $district->featured_image_id = $uploadedFile->id;
            }
            $district->slug = str_slug($request->name);
            $district->name = $request->name;
            $district->save();
            return redirect('/admin/district/list?id=' . $district->city_id);
        }
        return view('admin/district/edit', compact('district'));
    }

    /**
     * Create
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'id' => 'required',
                'featured_image' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }
            $district = new District();
            $city = City::find($request->id);
            $image = $request->featured_image;
            if ($image) {
                $uploadedFile = FileHelper::upload('district', $image);
                $district->featured_image_id = $uploadedFile->id;
            }
            $district->slug = str_slug($request->name);
            $district->name = $request->name;
            $district->city_id = $request->id;
            $district->state_id = $city->state_id;
            $district->country_id = $city->country_id;
            $district->save();
            return redirect('/admin/district/list?id=' . $request->id);
        }
        return view('admin/district/create');
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->query('id');
        $cityId = null;
        if (isset($id)) {
            $district = District::find($id);
            if (isset($district)) {
                $cityId = $district->city_id;
                FileHelper::removeFile($district->featured_image_id);
                $district->delete();
            }
        }
        return redirect('/admin/district/list?id=' .  $cityId);
    }

    /**
     * Status
     *
     * @return \Illuminate\Http\Response
     */
    public function status($id, $status)
    {
        $district = District::find($id);
        $district->status = $status;
        $district->save();
        return redirect('/admin/district/list?id=' . $district->city_id);
    }
}
