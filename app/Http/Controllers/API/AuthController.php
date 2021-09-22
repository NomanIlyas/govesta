<?php
namespace App\Http\Controllers\API;

use App\Helpers\AddressHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use App\Model\User\User;
use App\Model\User\SocialProvider;
use App\Model\User\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Mail\WelcomeEmail;
use App\Mail\ResetPasswordEmail;
use App\Mail\AgencyRegistered;
use Illuminate\Support\Facades\Mail;
use Socialite;

class AuthController extends Controller
{

    /**
     * Login
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'deleted_at' => null])) {
            $user = Auth::user();
            return APIResponse::success(array('token' => $user->createToken('MyApp')->accessToken, 'role' => $this->getRole($user)));
        } else {
            return APIResponse::error('Unauthorised', 401);
        }
    }

    /**
     * Register
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role' => 'required|in:agency,client',
            'company_name' => 'nullable|unique:agencies,name',
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $requests = $request->all();

        // Register
        $user = User::register($requests);

        if (isset($user)) {
            Mail::to($user->email)->queue(new WelcomeEmail($user));
            if ($requests['role'] == 'agency') {
                Mail::to(env('AGENCY_EMAIL'))->queue(new AgencyRegistered($user));
            }
            return APIResponse::success(array('token' => $user->createToken('MyApp')->accessToken, 'role' => $this->getRole($user)));
        }

        return APIResponse::error();
    }

    /**
     * Update Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        $requests = $request->all();
        $validator = Validator::make($requests, [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'company_name' => 'nullable|unique:agencies,name,' . (isset($user->agency) ? $user->agency->id : NULL),
            'website' => 'nullable',
            'about' => 'nullable',
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        // User
        $userModel = User::with(["basicInfo", "agency"])->find($user->id);
        $userModel->email = $requests['email'];
        $userModel->first_name = ucfirst($requests['first_name']);
        $userModel->last_name = ucfirst($requests['last_name']);
        $userModel->save();

        // Basic Info
        $userModel->basicInfo->website = $requests['website'];
        $userModel->basicInfo->about = $requests['about'];
        $userModel->basicInfo->save();

        // Agency
        if (isset($userModel->agency)) {
            $userModel->agency->name = ucfirst($requests['company_name']);
            $userModel->agency->slug = str_slug($requests['company_name']);
            $userModel->agency->save();
        }

        return $this->details();
    }

    /**
     * Update Address
     *
     * @return \Illuminate\Http\Response
     */
    public function address(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'country_id' => 'required|numeric',
            'city' => 'nullable|numeric',
            'state' => 'nullable|numeric',
            'district' => 'nullable|numeric',
            'street' => 'required|string',
            'street_number' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'google_place_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $address = User::with(['basicInfo', 'basicInfo.address'])->find($user->id)->basicInfo->address;
        $address = AddressHelper::parseAddress($address, $data);
        $address->save();
        return APIResponse::success();
    }

    /**
     * Get User Details
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        $profile = User::with(['basicInfo', 'agency', 'avatar', 'cover', 'basicInfo.address', 'basicInfo.address.state', 'basicInfo.address.city', 'basicInfo.address.district'])->find($user->id);
        return APIResponse::success($profile);
    }

    /**
     * Change Password
     *
     * @return \Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $user = Auth::user();
        $user->password = request('password');
        $user->save();
        return APIResponse::success();
    }

    /**
     * Logout
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user();
        $user->token()->revoke();
        return APIResponse::success(true);
    }

    /**
     * Verify
     *
     * @return \Illuminate\Http\Response
     */
    public function verify($token)
    {
        $user = User::where('activation_token', $token)->first();
        if (!$user) {
            return redirect(env('WEB_URL'));
        }
        $user->active = true;
        $user->activation_token = '';
        $user->save();
        return redirect(env('WEB_URL'));
    }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function social(String $provider)
    {
        $driver = Socialite::driver($provider);
        if ($provider == 'facebook') return $driver->asPopup()->redirect();
        return $driver->redirect();
    }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function socialFallback(String $provider)
    {
        $providerUser = Socialite::driver($provider)->user();
        $linkedSocialAccount = SocialProvider::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();
        $user = NULL;

        if ($linkedSocialAccount) {
            $user = $linkedSocialAccount->user;
        } else {

            if ($email = $providerUser->getEmail()) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $names = explode(" ", $providerUser->getName(), 2);
                $user = User::register([
                    'first_name' => $names[0],
                    'last_name' => $names[1],
                    'email' => $providerUser->getEmail(),
                    'avatar_url' => $providerUser->avatar_original,
                    'role' => 'client'
                ]);
            }

            $user->providers()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);
        }
        $token = $user->createToken('MyApp')->accessToken;
        $role = $this->getRole($user);
        return view('general/auth/token', compact('token', 'role'));
    }

    public function getRole($user)
    {
        if ($user->hasRole('client')) {
            return 'client';
        } else if ($user->hasRole('agency')) {
            return 'agency';
        }
        return;
    }

    /**
     * Reset Password
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return APIResponse::error('user_not_found');
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );

        Mail::to($user->email)->queue(new ResetPasswordEmail($user, $passwordReset->token));
        return APIResponse::success(1);
    }

    /**
     * Verify Reset Password
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $verified = PasswordReset::where('token', $request->token)->first();

        if (!$verified) {
            return APIResponse::success(false);
        }
        return APIResponse::success(true);
    }

    /**
     * Change Reset Password
     *
     * @return \Illuminate\Http\Response
     */
    public function changeResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return APIResponse::error($validator->errors(), 401);
        }

        $verified = PasswordReset::where('token', $request->token)->first();

        if (!$verified) {
            return APIResponse::success(false);
        }

        $user = User::where('email', $verified->email)->first();
        $user->password = request('password');
        $user->save();
        $verified->delete();

        return APIResponse::success(true);
    }
}
