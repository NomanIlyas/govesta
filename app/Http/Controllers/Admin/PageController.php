<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\General\Language;
use App\Model\Page\Page;
use Validator;

class PageController extends Controller
{

    /**
     * List
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = Page::paginate(15);
        return view('admin/page/index', compact('pages'));
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
                'id' => '',
                'locale' => 'required',
                'title' => 'required',
                'content' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $page = $request->id ? Page::find($request->id) : new Page();
            $page->translateOrNew($request->locale)->title = $request->title;
            $page->translateOrNew($request->locale)->slug = str_slug($request->title);
            $page->translateOrNew($request->locale)->content = $request->content;
            $page->save();
            return redirect('/admin/page/translations?id=' . $page->id);
        }
        $languages = Language::all();
        return view('admin/page/create', compact('languages'));
    }

    /**
     * Translations
     *
     * @return \Illuminate\Http\Response
     */
    public function translations(Request $request)
    {
        $id = $request->id;
        $page = Page::find($id);
        $translations = Page::find($id)->translations()->get();
        return view('admin/page/translations', compact('translations', 'page'));
    }

    /**
     * Edit
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = $request->validate([
            'id' => 'required',
            'locale' => 'required',
        ]);

        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'content' => '',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $page = Page::find($request->id);
            $page->translate($request->locale)->title = $request->title;
            $page->translate($request->locale)->slug = str_slug($request->title);
            $page->translate($request->locale)->content = $request->content;
            $page->save();
            return redirect('/admin/page/translations?id=' . $request->id);
        }
        $id = $request->id;
        $page = Page::find($id)->translate($request->locale);
        $languages = Language::all();
        return view('admin/page/edit', compact('languages', 'page'));
    }
}
