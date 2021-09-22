<?php
namespace App\Http\Controllers\Admin\Property;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\General\Language;
use App\Model\Property\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class FeatureController extends Controller
{

    /**
     * List
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $features = Feature::paginate(15);
        return view('admin/property/feature/index', compact('features'));
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $feature = Feature::find($id);
        if($feature) $feature->delete();
        return redirect('/admin/property/feature/list');
    }

    /**
     * Translations
     *
     * @return \Illuminate\Http\Response
     */
    public function translationList(Request $request)
    {
        $id = $request->id;
        $feature = Feature::find($id);
        $translations = Feature::find($id)->translations()->get();
        return view('admin/property/feature/translation.list', compact('translations', 'feature'));
    }

    /**
     * Create Translation
     *
     * @return \Illuminate\Http\Response
     */
    public function translationCreate(Request $request)
    {
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'id' => 'nullable',
                'locale' => 'required',
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $feature = $request->id ? Feature::find($request->id) : new Feature();
            $feature->translateOrNew($request->locale)->name = $request->name;
            $feature->translateOrNew($request->locale)->slug = str_slug($request->name);
            $feature->save();
            return redirect('/admin/property/feature/translation/list?id=' . $feature->id);
        }
        $languages = Language::all();
        return view('admin/property/feature/translation.create', compact('languages'));
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
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $feature = Feature::find($request->id);
            $feature->translate($request->locale)->name = $request->name;
            $feature->translate($request->locale)->slug = str_slug($request->name);
            $feature->save();
            return redirect('/admin/property/feature/translation/list?id=' . $feature->id);
        }
        $id = $request->id;
        $feature = Feature::find($id)->translate($request->locale);
        $languages = Language::all();
        return view('admin/property/feature/translation.edit', compact('languages', 'feature'));
    }

    /**
     * Delete Translation
     *
     * @return \Illuminate\Http\Response
     */
    public function translationDelete(Request $request)
    {
        $id = $request->id;
        $locale = $request->locale;
        if(isset($id) && isset($locale)) {
            $feature = Feature::find($id);
            if($feature) {
                $feature->deleteTranslations($locale);
            }
        }
        return redirect('/admin/property/feature/translation/list?id=' . $feature->id);
    }

}
