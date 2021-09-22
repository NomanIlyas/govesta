<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User\Agency;
use App\Model\User\User;
use App\Model\Property\Property;
use App\Model\Property\Image;
use App\Model\Geo\Address;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Enums\Status;
use Validator;

class AgencyController extends Controller
{

    /**
     * List
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $term = $request->term;
        $agencies = Agency::where('name', 'like', "%$term%")->with(['user'])->withCount(['properties as online_property' => function ($query) {
            $query->where('status', Status::Published);
        }, 'properties as pause_property' => function ($query) {
            $query->where('status', Status::Draft);
        }])->paginate(20);
        return view('admin/agency/index', compact('agencies'));
    }

    /**
     * Edit
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->query('id');
        $agency = Agency::find($id);
        if ($request->method() == "POST") {
            $validator = Validator::make($request->all(), [
                'cpc' => 'required|numeric',
                'analytics_links' => ''
            ]);

            if ($validator->fails()) {
                $request->session()->flash('error', $validator->messages()->all());
                return redirect()->back()->withInput();
            }

            $agency->cpc = $request->cpc;
            $agency->analytics_links = $request->analytics_links;
            $agency->save();

            return redirect('/admin/agency/list');
        }
        return view('admin/agency/edit', compact('agency'));
    }

    /**
     * Status
     *
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'status' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $request->session()->flash('error', $validator->messages()->all());
            return redirect()->back()->withInput();
        }

        $agency = Agency::find($request->id);
        $agency->status = $request->status;
        $agency->save();

        return redirect('/admin/agency/list');
    }

    /**
     * Delete
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $request->session()->flash('error', $validator->messages()->all());
            return redirect()->back()->withInput();
        }

        if ($request->method() == "POST") {
            $properties = Property::where('agency_id', $request->id)->get();
            foreach ($properties as $property) {
                $images = Image::where('property_id', $property->id)->get();
                foreach ($images as $image) {
                    FileHelper::removeFile($image->file_id);
                    Address::where('id', $property->address_id)->delete();
                }
            }
            $agency = Agency::find($request->id);
            $user = User::find($agency->user_id);
            $user->delete();
            return redirect('/admin/agency/list');
        }
        return view('admin/agency/delete');
    }
}
