<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\User,
    App\Client;
use Session;

/**
 * Class AuthController - custom class for Registration and Authentication.
 */
class AuthController extends Controller
{
    /**
     * Method for getting login form page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function login()
	{
	    return view('auth.login');
	}

    /**
     * Method for user authentication.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
	public function loginPost(Request $request)
    {
        $remember = $request->input('remember') ? true : false;

		$authResult = Auth::attempt([
			'email' => $request->input('email'),
			'password' => $request->input('password'),
		], $remember);
		
		if ($authResult) {
			return redirect()->route('main');
		} 
		else {
			return redirect()
				->route('loginPage')
				->with('authError', 'Данные не верны.');
		}
	}

	public function getPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $password = $this->generatePassword();

            $user->update([
                'password' => bcrypt($password),
            ]);

            if (ENV('APP_URL') == 'http://telegram-bot') {
                $user->update([
                    'password' => bcrypt(0),
                ]);
                return 1;
            }
            else {

                if ($user->admin_uid) {
                    $client = Client::where('uid', $user->admin_uid)->first();
                    $client->sendMessage($password, $client->uid);

                    return 1;
                } else {
                    return 'Пользователь не привязан к аккаунту в Телеграм.';
                }
            }
        }
        else {
            return 'Пользователь не найден.';
        }
    }

    /**
     * Method for log out.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
	public function logout()
	{
	    Auth::user()->update([
            'password' => bcrypt($this->generatePassword()),
        ]);

		Auth::logout();
		return redirect()->route('loginPage');
	}

	private function generatePassword()
    {
        return substr(md5(uniqid("")), 0, 10);
    }
}