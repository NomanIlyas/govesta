<?php
namespace App\Http\Controllers\Admin\Property;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\General\Language;
use App\Model\Property\Type;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class TypeController extends Controller
{

    /**
     * List
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $types = Type::paginate(15);
        return view('admin/property/type/index', compact('types'));
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $type = Type::find($id);
        if($type) $type->delete();
        return redirect('/admin/property/type/list');
    }

    /**
     * Translations
     *
     * @return \Illuminate\Http\Response
     */
    public function translationList(Request $request)
    {
        $id = $request->id;
        $type = Type::find($id);
        $translations = Type::find($id)->translations()->get();
        return view('admin/property/type/translation.list', compact('translations', 'type'));
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

            $type = $request->id ? Type::find($request->id) : new Type();
            $type->translateOrNew($request->locale)->name = $request->name;
            $type->translateOrNew($request->locale)->slug = str_slug($request->name);
            $type->save();
            return redirect('/admin/property/type/translation/list?id=' . $type->id);
        }
        $languages = Language::all();
        return view('admin/property/type/translation.create', compact('languages'));
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

            $type = Type::find($request->id);
            $type->translate($request->locale)->name = $request->name;
            $type->translate($request->locale)->slug = str_slug($request->name);
            $type->save();
            return redirect('/admin/property/type/translation/list?id=' . $type->id);
        }
        $id = $request->id;
        $type = Type::find($id)->translate($request->locale);
        $languages = Language::all();
        return view('admin/property/type/translation.edit', compact('languages', 'type'));
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
            $type = Type::find($id);
            if($type) {
                $type->deleteTranslations($locale);
            }
        }
        return redirect('/admin/property/type/translation/list?id=' . $type->id);
    }

}
