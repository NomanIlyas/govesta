<?php
namespace App\Http\Controllers\Admin\Property;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\General\Language;
use App\Model\Property\SubType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class SubTypeController extends Controller
{

    /**
     * List
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tid = $request->tid;
        $stypes = isset($tid) ? SubType::where('type_id', $tid)->paginate(15) : SubType::paginate(15);
        return view('admin/property/stype/index', compact('stypes', 'tid'));
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $stype = SubType::find($id);
        $tid = $stype->type_id;
        if($stype) $stype->delete();
        return redirect('/admin/property/stype/list?tid='. $tid);
    }

    /**
     * Translations
     *
     * @return \Illuminate\Http\Response
     */
    public function translationList(Request $request)
    {
        $id = $request->id;
        $stype = SubType::find($id);
        $translations = SubType::find($id)->translations()->get();
        return view('admin/property/stype/translation.list', compact('translations', 'stype'));
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
                'tid' => 'required',
                'locale' => 'required',
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $stype = $request->id ? SubType::find($request->id) : new SubType();
            $stype->type_id = $request->tid;
            $stype->translateOrNew($request->locale)->name = $request->name;
            $stype->translateOrNew($request->locale)->slug = str_slug($request->name);
            $stype->save();
            return redirect('/admin/property/stype/translation/list?id='. $stype->id);
        }
        $languages = Language::all();
        return view('admin/property/stype/translation.create', compact('languages'));
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

            $stype = SubType::find($request->id);
            $stype->translate($request->locale)->name = $request->name;
            $stype->translate($request->locale)->slug = str_slug($request->name);
            $stype->save();
            return redirect('/admin/property/stype/translation/list?id=' . $stype->id);
        }
        $id = $request->id;
        $stype = SubType::find($id)->translate($request->locale);
        $languages = Language::all();
        return view('admin/property/stype/translation.edit', compact('languages', 'stype'));
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
            $stype = SubType::find($id);
            if($stype) {
                $stype->deleteTranslations($locale);
            }
        }
        return redirect('/admin/property/stype/translation/list?id=' . $stype->id);
    }

}
