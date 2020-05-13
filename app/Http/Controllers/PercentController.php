<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Category,
    App\Percent;
use Illuminate\Support\Facades\DB;

class PercentController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return redirect('/login');
        }
        elseif (Auth::user()->access !== 5) {
            abort(403);
        }

        $categories = Category::where('parent_id', 0)
            ->orderBy('position', 'ASC')
            ->get();

        $data = [
            'title' => 'Процент',
            'categories' => $categories,
        ];

        return view('percent', $data)
            ->with(["page" => "percent"]);
    }

    public function update_checkbox(Request $request)
    {
        $explode = explode('_', $request->id_value);
        $new_value = $explode[0];
        $id = $explode[2];
        $item = Percent::find($id);
        $cell = $explode[1];
        $item->update([$cell => $new_value]);

        if ($item->category->parent_id == 0) {
            $childrenIds = Category::where('parent_id', $item->category_id)->pluck('id')->toArray();
            $items = Percent::whereIn('category_id', $childrenIds)->get();
            foreach ($items as $item) {
                $item->update([
                    $cell => $new_value,
                ]);
            }
        }
    }
}
