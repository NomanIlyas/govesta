<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\General\Language;
use App\Model\Geo\City;
use App\Model\Geo\State;
use Illuminate\Http\Request;
use Validator;

class CityController extends Controller
{

    /**
     * States
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $state = $request->state;
        $term = $request->term;
        $cities = City::where('state_id', $state)->whereTranslationLike('name', "%$term%")->with(['state', 'country'])->paginate(15);
        return view('admin/city/index', compact('cities', 'state'));
    }

    /**
     * Cretae City
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $stateId = $request->state;
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'featured_image' => 'required',
                'state_id' => 'required',
                'name' => 'required',
                'description' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }
            $state = State::find($request->state_id);
            if (isset($state)) {
                $image = $request->featured_image;
                $uploadedFile = FileHelper::upload('state', $image);
                $city = City::create([
                    'country_id' => $state->country_id,
                    'state_id' => $state->id,
                    'featured_image_id' => $uploadedFile->id,
                ]);
                $city->translateOrNew('en')->name = $request->name;
                $city->translateOrNew('en')->slug = str_slug($request->name);
                $city->translateOrNew('en')->description = $request->description;
                $city->save();
            }
            return redirect('/admin/city/list?state=' . $stateId);
        }
        return view('admin/city/create', compact('stateId'));
    }

    /**
     * Edit City
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->query('id');
        $city = City::find($id);
        if ($request->method() == "POST") {
            $validator = $request->validate([
                'featured_image' => '',
            ]);
            $image = $request->featured_image;
            if (isset($image)) {
                if ($city->featured_image_id) {
                    FileHelper::removeFile($city->featured_image_id);
                }
                $uploadedFile = FileHelper::upload('city', $image);
                $city->featured_image_id = $uploadedFile->id;
                $city->save();
            }
            return redirect('/admin/city/list');
        }
        return view('admin/city/edit', compact('city'));
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->query('id');
        $stateId = null;
        if (isset($id)) {
            $city = City::find($id);
            if (isset($city)) {
                $stateId = $city->state_id;
                FileHelper::removeFile($city->featured_image_id);
                $city->delete();
            }
        }
        return redirect('/admin/city/list?state=' .  $stateId);
    }

    /**
     * Status
     *
     * @return \Illuminate\Http\Response
     */
    public function status($id, $status)
    {
        $city = City::find($id);
        $city->status = $status;
        $city->save();
        return redirect('/admin/city/list?state=' . $city->state_id);
    }

    /**
     * Translations
     *
     * @return \Illuminate\Http\Response
     */
    public function translationList(Request $request)
    {
        $id = $request->id;
        $city = City::find($id);
        $translations = City::find($id)->translations()->get();
        return view('admin/city/translation.list', compact('translations', 'city'));
    }

    /**
     * CREATE TRANSLATION
     *
     * @return \Illuminate\Http\Response
     */
    public function translationCreate(Request $request)
    {
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'locale' => 'required',
                'name' => 'required',
                'description' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $city = City::find($request->id);
            $city->translateOrNew($request->locale)->name = $request->name;
            $city->translateOrNew($request->locale)->slug = str_slug($request->name);
            $city->translateOrNew($request->locale)->description = $request->description;
            $city->save();
            return redirect('/admin/city/translation/list?id=' . $request->id);
        }
        $languages = Language::all();
        return view('admin/city/translation.create', compact('languages'));
    }

    /**
     * Edit Translation
     *
     * @return \Illuminate\Http\Response
     */
    public function translationEdit(Request $request)
    {
        $validator = $request->validate([
            'id' => 'required',
            'locale' => 'required',
        ]);

        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $city = City::find($request->id);
            $city->translate($request->locale)->name = $request->name;
            $city->translate($request->locale)->slug = str_slug($request->name);
            $city->translate($request->locale)->description = $request->description;
            $city->save();
            return redirect('/admin/city/translation/list?id=' . $request->id);
        }
        $id = $request->id;
        $city = City::find($id)->translate($request->locale);
        $languages = Language::all();
        return view('admin/city/translation.edit', compact('languages', 'city'));
    }
}
