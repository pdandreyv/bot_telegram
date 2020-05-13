<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User,
    App\Percent,
    App\Category,
    App\Client;
use Auth;

class UserController extends Controller
{	

	public function index()
    {   

        if (Auth::guest()) {
            return redirect('/login');
        }
        elseif (Auth::user()->access == 3) {
            return redirect('/discount');
        }
        elseif (in_array(Auth::user()->access, [5, 6, 2])) {
            abort(403);
        }

        $data = [
            'title' => 'Пользователи',
            'menu_list' => 'active',
            'users' => User::all(),
            'regions' => Client::groupBy('city')->pluck('city'),
        ];
        return view('users', $data)->with(["page" => "user"]);
    }

    public function getRegions()
    {
        $regions = Client::groupBy('city')
            ->pluck('city');
        $data = [
            'regions' => $regions,
        ];

        return view('parts.regions_list', $data);
    }

    public function update_checkbox(Request $request)
    {
        $explode = explode('_', $request->id_value);
        $new_value = $explode[0];
        $cell = $explode[1];
        $id = $explode[2];

        User::where('id', $id)
            ->update([$cell => $new_value]);
    }

    public function show($id)
    {
        $user = User::find($id);
        $data = [
            'user' => $user,
            'regions' => Client::groupBy('city')->pluck('city'),
            'userRegions' => explode(',', $user->regions),
        ];

        return view('ajax_templates.user_editing', $data);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100|unique:users,name,',
            'email' => 'required|max:100|email|unique:users,email,',
            //'password' => 'required|min:6|max:12',
            'access' => 'required',
            'admin_uid' => 'required|unique:users,admin_uid',
        ]);

        if ($request->regions) {
            $regions = implode(',', $request->regions);
        }
        else {
            $regionsAll = Client::groupBy('city')
                ->where('city', '!=','')
                ->pluck('city')
                ->toArray();
            $regions = implode(',', $regionsAll);
        }

        if($request->admin_uid != null && $request->access == 0) {
            $client = Client::where('uid', '=', $request->admin_uid)->first();
            if ($client) {
                $client->admin = 1;
                $client->save();

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt(uniqid("")),
                    'access' => $request->access,
                    'regions' => $regions,
                    'admin_uid' => $request->admin_uid,
                    'keyword' => $request->keyword,
                ]);
            } else {
                $message = 'Клиент с uid ' . $request->admin_uid . ' не найден в базе';
                $request->session()->flash('clientNotFound', $message);
            }
        }
        else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt(uniqid("")),
                'access' => $request->access,
                'regions' => $regions,
                'admin_uid' => $request->admin_uid,
                'keyword' => $request->keyword,
            ]);
        }

        if ($request->access == 5) {
            $categories = Category::pluck('id')->toArray();
            foreach ($categories as $one) {
                Percent::create([
                    'user_id' => $user->id,
                    'category_id' => $one,
                ]);
            }
        }

        return back();
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:100|unique:users,name,'.$id,
            'email' => 'required|max:100|email|unique:users,email,'.$id,
            'admin_uid' => 'required|unique:users,admin_uid,'.$id,
      	]);

      $user = User::find($id);

      $user->name = $request->name;
      $user->email = $request->email;
      $user->access = $request->access;

      if ($request->access == 5) {
          $user->keyword = $request->keyword;
      }
      else {
          $user->keyword = null;
      }

      if($request->admin_uid != null && $request->access == 0) {
          $client = Client::where('uid', '=', $request->admin_uid)->first();
          if ($client) {
              $client->admin = 1;
              $client->save();
          }
      }

      if ($request->access == 2) {
          if ($request->regions) {
              $regions = implode(',', $request->regions);
          } else {
              $regionsAll = Client::groupBy('city')
                  ->where('city', '!=', '')
                  ->pluck('city')
                  ->toArray();
              $regions = implode(',', $regionsAll);
          }
      } else {
          $regions = null;
      }

      $user->regions = $regions;

      if($request->admin_uid != $user->admin_uid || $request->access != 0) {
          //деактивируем права админа у клиента по старому uid
          $clientOld = Client::where('uid', '=', $user->admin_uid)->first();
          if($clientOld) {
              $clientOld->admin = 0;
              $clientOld->save();
          }

          $user->admin_uid = $request->admin_uid;
      }

        if ($request->access == 5) {
            $categories = Category::pluck('id')->toArray();
            foreach ($categories as $one) {
                Percent::create([
                    'user_id' => $user->id,
                    'category_id' => $one,
                ]);
            }
        }

      $user->save();

      return back();
    }


    public function delete($id)
    {
        $user = User::find($id);
        //деактивируем права админа у клиента по uid
        $client = Client::where('uid', '=', $user->admin_uid)->first();
        if ($client) {
            $client->admin = 0;
            $client->save();
        }

        //Отвязываем клиентов при удалении перекупа
        if ($user->access == 5) {
            $clients = Client::where('user_id', $id)->get();
            foreach ($clients as $client) {
                $client->update([
                    'user_id' => null,
                ]);
            }
        }
        $user->delete();

        return back();
    }
}
