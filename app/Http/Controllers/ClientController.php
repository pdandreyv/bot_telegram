<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Client;
use App\Category;
use App\User;
use Auth;

class ClientController extends Controller
{
    public function index()
    {   
        if (Auth::guest()) {
            return redirect('/login');
        }
        
        $data = [
            'ajax_info' => 'client',
            'title' => 'Клиенты',
        ];
        
		return view('clients', $data)->with(["page" => "client"]);
    }

    public function update(Request $request)
    {
        $explode = explode('-', $request->id_value);
        $new_value = $request->new_value;
        $cell = $explode[0];
        $id = $explode[1];
        $client = Client::find($id);

        if ($cell == 'current_amount') {
            $client->update([
                'max_amount' => $new_value,
                'current_amount' => $new_value,
            ]);

            echo number_format($new_value, 0, '.', '.');
        }
        else {
            $client->update([$cell => $new_value]);
            echo $new_value;
        }
    }

    public function changeActivity(Request $request)
    {
        $client = Client::findOrFail($request->id);
        if ($client->active == 1) {
            $client->update([
                'active' => 0,
            ]);
        }
        elseif ($client->active == 0) {
            $client->update([
                'active' => 1,
            ]);
        }
    }

    public function changeReceipts(Request $request)
    {
        $client = Client::findOrFail($request->id);
        if ($client->showReceipts == 1) {
            $client->update([
                'showReceipts' => 0,
            ]);
        }
        elseif ($client->showReceipts == 0) {
            $client->update([
                'showReceipts' => 1,
            ]);
        }
    }

    public function update_checkbox(Request $request)
    {
        $explode = explode('_', $request->id_value);
        $new_value = $explode[0];
        $id = $explode[2];
        $client = Client::find($id);

        if ($explode[1] == 'paymentType') {
            $cell = 'payment_type_id';
            $client->update([
                $cell => $new_value,
                'max_amount' => $client->current_amount,
            ]);
        }
        else {
            $cell = $explode[1];
            $client->update([$cell => $new_value]);
        }
    }


    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();

        return back();
    }

    public function change_type($id, $value)
    {
        $client = Client::findOrFail($id);
        $client->update(['type' => $value]);
    }

    public function changeNumber(Request $request)
    {
        $data = explode('+', $request->data);

        $id = $data[1];
        $value = $data[0];

        $client = Client::findOrFail($id);
        $client->update(['unique_number' => $value]);
    }

    public function activate_all(Request $request)
    {
        DB::table('clients')->update(['active' => $request->value]);
        if($request->value)
            echo "Все клиенты активированы";
        else 
            echo "Все клиенты деактивированы";
        exit;
    }

    public function getClient(Request $request) {
        return Client::find($request->id);
    }

    public function sendOrder(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        $text = $client->formOrder($client->id);

        return $client->sendMessage($text, $client->uid);
    }
	
    public function search_ajax(Request $request) 
    {
        $new_value = $request->new_value;


        $clients = Client::where('first_name','LIKE', "%" . $new_value . "%")
            ->orWhere('last_name','LIKE', "%" . $new_value . "%")
            ->orWhere('username','LIKE', "%" . $new_value . "%")
            ->orWhere('uid','LIKE', "%" . $new_value . "%")
            ->orWhere('country','LIKE', "%" . $new_value . "%")
            ->orWhere('city','LIKE', "%" . $new_value . "%")
            ->orWhere('created_at','LIKE', "%" . $new_value . "%")->get();
        
        //echo "<tr><td> test </td></tr>";

        foreach ($clients as $client) {
        if ($client->type == 1) {
                $opt = "<input id='0_type_" . $client->id . "' type='checkbox' checked onclick=check_checkbox('0_type_" . $client->id . "')>
                    <span>  </span>";
        }else {
                $opt = "<input id='1_type_" . $client->id . "' type='checkbox' onclick=check_checkbox('1_type_" . $client->id . "')>
                    <span>  </span>";
        }

        if ($client->active == 1) {
                $resolution = "<input type='checkbox' checked onclick=check_checkbox('0_active_" . $client->id . "')>";
        }else {
                $resolution = "<input type='checkbox' onclick=check_checkbox('1_active_" . $client->id . "')>";
        }
        echo "
        <tr>
            <td>
                <a id='a-first_name-" . $client->id . "' onclick=view_input('first_name-" . $client->id . "', 'clients')>" . $client->first_name . "</a>
                <input class='edit_info' type='text' id='first_name-" . $client->id . "' value='" . $client->first_name . "'>
            </td>
            <td>
                <a id='a-last_name-" . $client->id . "' onclick=view_input('last_name-" . $client->id . "', 'clients')>" . $client->last_name . "</a>
                <input class='edit_info' type='text' id='last_name-" . $client->id . "' value='" . $client->last_name . "'>
            </td>
            <td>" . $client->user_name . "</td>
            <td class='td_center_text'>" . $client->uid . "</td>
            <td>
                <a id='a-city-" . $client->id . "' onclick=view_input('city-" . $client->id . "', 'clients')>" . $client->city . "</a>
                <input class='edit_info' type='text' id='city-" . $client->id . "' value='" . $client->city . "'>
            </td>
            <td class='td_center_text'>
                " . $opt . "
            </td>
            <td>" . $client->created_at . "</td>
            <td class='td_center_text'>
                " . $resolution . "
            </td>
            <td><a href='" . url('/history/'.$client->id) . "'>История</td>
            <td class='td_center_text'>
            <form action='" . url('/clients/'.$client->id) . "' method='POST'>
                <input type='hidden' name='_method' value='DELETE'>
                <input type='hidden' name='_token' value='" . csrf_token() . "'>
                <button class='btn btn-danger btn-xs'>x</button>
            </form>
            </td>
        </tr>";
        /*
        echo "
        <tr>
            <td class='td_center_text'>
                <a id='a-position-" . $product->id . "' onclick=view_input('position-" . $product->id . "', 'products')>" . $product->id . "</a>
                <input class='edit_info' type='text' id='position-" . $product->id . "' value='" . $product->position . "'>
            </td>
            <td>" . $product->cat_name . "</td>
            <td>
                <a id='a-name-" .$product->id. "' onclick=view_input('name-" .$product->id. "', 'products')>" .$product->name. "</a>
                <input class='edit_info' type='text' id='name-" .$product->id. "' value='" .$product->name. "'>
            </td>
            <td>
                <a id='a-country-" .$product->id. "' onclick=view_input('country-" .$product->id. "', 'products')>" .$product->country. "</a>
                <input class='edit_info' type='text' id='country-" .$product->id. "' value='" .$product->country. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-quantity-" .$product->id. "' onclick=view_input('quantity-" .$product->id. "', 'products')>" .$product->quantity. "</a>
                <input class='edit_info' type='text' id='quantity-" .$product->id. "' value='" .$product->quantity. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-one_hand-" .$product->id. "' onclick=view_input('one_hand-" .$product->id. "', 'products')>" .$product->one_hand. "</a>
                <input class='edit_info' type='text' id='one_hand-" .$product->id. "' value='" .$product->one_hand. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-price-" .$product->id. "' onclick=view_input('price-" .$product->id. "', 'products')>" .$product->price. "</a>
                <input class='edit_info' type='text' id='price-" .$product->id. "' value='" .$product->price. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-price_opt-" .$product->id. "' onclick=view_input('price_opt-" .$product->id. "', 'products')>" .$product->price_opt. "</a>
                <input class='edit_info' type='text' id='price_opt-" .$product->id. "' value='" .$product->price_opt. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-addition_count-" .$product->id. "' onclick=view_input('addition_count-" .$product->id. "', 'products')>" .$product->addition_count. "</a>
                <input class='edit_info' type='text' id='addition_count-" .$product->id. "' value='" .$product->addition_count. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-addition_price-" .$product->id. "' onclick=view_input('addition_price-" .$product->id. "', 'products')>" .$product->addition_price. "</a>
                <input class='edit_info' type='text' id='addition_price-" .$product->id. "' value='" .$product->addition_price. "'>
            </td>
            <td>

                <i class='fa fa-cloud-download cl-search-icon' aria-hidden='true' onclick=search_image(" .$product->id. ")></i>

                <form method='post' action='" . url('/products/image') . "' enctype='multipart/form-data'>
                    <input type='hidden' name='image_name' value='" .$product->id. "'> 
                    <input type='hidden' name='_token' value='" . csrf_token() . "'>
                    <input id='image-" .$product->id. "' onchange='click_button(" .$product->id. ")' class='image_input' type='file' name='images'>
                    <button id='submit-" .$product->id. "' class='image_input' type='submit'>Отправить</button>
                </form>
            </td>
            <td class='td_center_text'>
            <form action='" . url('/products/'.$product->id) . "' method='POST'>
                <input type='hidden' name='_method' value='DELETE'>
                <input type='hidden' name='_token' value='" . csrf_token() . "'>
                <button class='btn btn-danger btn-xs'>x</button>
            </form>
            </td>
        </tr>";
        */
        }
        
    }
}
