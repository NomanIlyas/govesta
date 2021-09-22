<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\General\Language;
use App\Model\Geo\State;
use App\Model\Geo\Country;
use Illuminate\Http\Request;
use Validator;

class StateController extends Controller
{
    /**
     * States
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $country = $request->country;
        $term = $request->term;
        $query = State::whereTranslationLike('name', "%$term%");
        if (isset($country)) {
            $query->where('country_id', $country);
        }
        $states = $query->with(['country'])->paginate(15);
        return view('admin/state/index', compact('states'));
    }

    /**
     * Cretae State
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'featured_image' => 'required',
                'country_id' => 'required',
                'name' => 'required',
                'description' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $image = $request->featured_image;
            $uploadedFile = FileHelper::upload('state', $image);
            $state = State::create([
                'country_id' => $request->country_id,
                'featured_image_id' => $uploadedFile->id,
            ]);

            $state->translateOrNew('en')->name = $request->name;
            $state->translateOrNew('en')->slug = str_slug($request->name);
            $state->translateOrNew('en')->description = $request->description;
            $state->save();
            return redirect('/admin/state/list?country=' . $state->country_id);
        }
        $countries = Country::all();
        return view('admin/state/create', compact('countries'));
    }

    /**
     * Edit State
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->query('id');
        $state = State::find($id);
        if ($request->method() == "POST") {
            $validator = $request->validate([
                'featured_image' => '',
            ]);
            $image = $request->featured_image;
            if (isset($image)) {
                if ($state->featured_image_id) {
                    FileHelper::removeFile($state->featured_image_id);
                }
                $uploadedFile = FileHelper::upload('state', $image);
                $state->featured_image_id = $uploadedFile->id;
                $state->save();
            }
            return redirect('/admin/state/list');
        }
        return view('admin/state/edit', compact('state'));
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->query('id');
        if (isset($id)) {
            $state = State::find($id);
            if (isset($state)) {
                FileHelper::removeFile($state->featured_image_id);
                $state->delete();
            }
        }
        return redirect('/admin/state/list');
    }

    /**
     * Status
     *
     * @return \Illuminate\Http\Response
     */
    public function status($id, $status)
    {
        $state = State::find($id);
        $state->status = $status;
        $state->save();
        return redirect('/admin/state/list?country=' . $state->country_id);
    }

    /**
     * Translations
     *
     * @return \Illuminate\Http\Response
     */
    public function translationList(Request $request)
    {
        $id = $request->id;
        $state = State::find($id);
        $translations = State::find($id)->translations()->get();
        return view('admin/state/translation.list', compact('translations', 'state'));
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

            $state = State::find($request->id);
            $state->translateOrNew($request->locale)->name = $request->name;
            $state->translateOrNew($request->locale)->slug = str_slug($request->name);
            $state->translateOrNew($request->locale)->description = $request->description;
            $state->save();
            return redirect('/admin/state/translation/list?id=' . $request->id);
        }
        $languages = Language::all();
        return view('admin/state/translation.create', compact('languages'));
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

            $state = State::find($request->id);
            $state->translate($request->locale)->name = $request->name;
            $state->translate($request->locale)->slug = str_slug($request->name);
            $state->translate($request->locale)->description = $request->description;
            $state->save();
            return redirect('/admin/state/translation/list?id=' . $request->id);
        }
        $id = $request->id;
        $state = State::find($id)->translate($request->locale);
        $languages = Language::all();
        return view('admin/state/translation.edit', compact('languages', 'state'));
    }
}
