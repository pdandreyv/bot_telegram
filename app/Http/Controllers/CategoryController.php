<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category,
    App\Percent,
    App\User;
use Auth;

class CategoryController extends Controller
{
    public function index()
    {   
        if (Auth::guest()) {
            return redirect('/login');
        }
        elseif (Auth::user()->access == 5 || Auth::user()->access == 6) {
            abort(403);
        }

        $data = [
            'title' => 'Категории',
        ];

        return view('category', $data)->with(["page" => "category"]);
    }

    public function create(Request $request)
    {
       $this->validate($request, [
            'name' => 'required|max:100',
            'parent_id' => 'required'
       ]);

       if ($request->parent_id == 'parent') {
           $posMax = Category::where('parent_id', '=', 0)
               ->pluck('position')
               ->max();

           $category = Category::create([
               'name' => $request->name,
               'parent_id' => 0,
               'position' => ++$posMax,

           ]);
       } else {
           $posMax = Category::where('parent_id', '=', $request->parent_id)
               ->pluck('position')
               ->max();

           $category = Category::create([
               'name' => $request->name,
               'parent_id' => $request->parent_id,
               'position' => ($posMax + 10),
           ]);
       }

        $resellers = User::where('access', 5)->pluck('id')->toArray();
        foreach ($resellers as $one) {
            Percent::create([
                'user_id' => $one,
                'category_id' => $category->id,
            ]);
        }

       return back();
    }

    public function changePosition(Request $request)
    {
        $current = Category::where('id', $request->id_current)->first();
        $currentPos = $current->position;
        $sibling = Category::where('id', $request->id_sibling)->first();

        $current->update([
            'position' => $sibling->position,
        ]);

        $sibling->update([
            'position' => $currentPos,
        ]);
    }

    public function getChildren($category_id)
    {
        $data = [
            'children' => Category::where('parent_id', $category_id)
                ->orderBy('position', 'ASC')
                ->get(),
            'category_id' => $category_id,
        ];

        return view('ajax_templates.categories_children', $data);
    }

    public function update(Request $request)
    {
        $explode = explode('-', $request->id_value);
        $new_value = $request->new_value;
        $cell = $explode[0];
        $id = $explode[1];
        Category::where('id', $id)
            ->update([$cell => $new_value]);
        echo $new_value;
    }

    public function changeWorkingTime(Request $request)
    {
        $explode = explode('-', $request->id_value);

        $new_value = $explode[2];
        $cell = $explode[1];
        $id = $explode[0];

        Category::where('id', $id)
                ->update([$cell => $new_value]);

        echo $new_value;
    }

    public function changeVisibility(Request $request)
    {
        $category = Category::find($request->id);

        if ($category->visible == 1) {
            $category->update([
                'visible' => 0,
            ]);
        }
        elseif ($category->visible == 0) {
            $category->update([
                'visible' => 1,
            ]);
        }

        if ($category->parent_id === 0) {
            return 'parent';
        }
        else {
            return 'child';
        }
    }

    public function destroy($id)
    {
        Category::find($id)->delete();

        return back();
    }
}
