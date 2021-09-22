<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use Illuminate\Http\Request;
use App\Mail\ContactEmail;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

class MailController extends Controller
{

    public function contact(Request $request)
    {
        $params = $request->all();
        $client = new Client([]);

        $orgId = null;
        if(isset($params['agency_name'])) {
            $organization = $client->request('POST', 'https://companydomain.pipedrive.com/v1/organizations?api_token=1351b29c4f38bb496a5300e9f6dff129fe374df3', [
                'json' => [
                    'name' => $params['agency_name']
                ]
            ]);
            $data = json_decode($organization->getBody());
            $orgId = $data->data->id;
        }
        
        $r = $client->request('POST', 'https://companydomain.pipedrive.com/v1/persons?api_token=1351b29c4f38bb496a5300e9f6dff129fe374df3', [
            'json' => [
                'email' => $params['email_address'],
                'name' => $params['first_name'] ?? 'No Name',
                'org_id' => $orgId,
                'ab48fc2d07b543bd4b5766671993ea6c3877fcdf' => $params['first_name'] ?? '',
                'a62398d40f742b8978a6318f07353437713b63bf' => $params['last_name'] ?? '',
                'phone' => $params['phone'] ?? '',
                'c5b2178d58ea285883f2ba5d3d5162c66a331bc1' => $params['agency_name'] ?? '',
                '84375081ce997434ab57fce2bb785880079688b2' => $params['city'] ?? ''
            ]
        ]);
        Mail::to(env('AGENCY_EMAIL'))->queue(new ContactEmail($params));
        return APIResponse::success(1);
    }
}
