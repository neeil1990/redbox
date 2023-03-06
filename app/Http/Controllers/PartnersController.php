<?php

namespace App\Http\Controllers;

use App\PartnersGroups;
use App\PartnersItems;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PartnersController extends Controller
{
    public function partners()
    {
        $admin = User::isUserAdmin();
        $groups = PartnersGroups::with('items')->get()->sortBy('position');

        foreach ($groups as $key => $group) {
            foreach ($group['items'] as $partnerKey => $item) {
                if ($item["auditorium_" . Auth::user()->lang] === 0) {
                    unset($group['items'][$partnerKey]);
                }
            }

            if (count($group['items']) > 0) {
                $groups[$key]['items'] = collect($group['items'])->sortBy('position')->toArray();
            } else {
                unset($groups[$key]);
            }
        }

        return view('partners.index', compact('groups', 'admin'));
    }

    public function admin()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $groups = PartnersGroups::with('items')->get()->sortBy('position');

        foreach ($groups as $key => $group) {
            if (count($group['items']) > 0) {
                $groups[$key]['items'] = collect($group['items'])->sortBy('position')->toArray();
            }
        }

        return view('partners.admin', compact('groups'));
    }

    public function addGroup()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        return view('partners.add-group');
    }

    public function saveGroup(Request $request): ?RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $this->validate($request, [
            'name' => ['required', 'unique:partners_groups'],
            'position' => ['required', 'unique:partners_groups'],
        ], [
            'position.unique' => __('This position already exists'),
            'position.required' => __('The position of the group cannot be empty'),
            'name.unique' => __('Such a group already exists'),
            'name.required' => __('The name of the group cannot be empty'),
        ]);

        $group = new PartnersGroups($request->all());
        $group->save();

        flash()->overlay(__('The group was successfully created'), ' ')->success();

        return Redirect::route('partners.admin');
    }

    public function editGroupView(PartnersGroups $group)
    {
        return view('partners.edit-group', compact('group'));
    }

    public function editGroup(Request $request): RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $group = PartnersGroups::findOrFail($request->id);
        $this->validate($request, [
            'name' => ['required', Rule::unique('partners_groups')->ignore($group->name, 'name')],
            'position' => ['required', Rule::unique('partners_groups')->ignore($group->position, 'position')],
        ], [
            'position.unique' => __('This position already exists'),
            'position.required' => __('The position of the group cannot be empty'),
            'name.unique' => __('Such a group already exists'),
            'name.required' => __('The name of the group cannot be empty'),
        ]);

        $group->name = $request->name;
        $group->position = $request->position;
        $group->save();

        flash()->overlay(__('A group was successfully edited'), ' ')->success();

        return Redirect::route('partners.admin');
    }

    public function removeGroup(Request $request): JsonResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $group = PartnersGroups::findOrFail($request->id);
        if (count($group['items']) > 0) {
            foreach ($group['items'] as $item) {
                $item->delete();
            }
        }
        $group->delete();

        return response()->json([], 200);
    }

    public function addItem()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $groups = PartnersGroups::get();

        return view('partners.add-item', compact('groups'));
    }

    public function saveItem(Request $request): ?RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $this->validate($request, [
            'image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|unique:partners_items',
            'link' => 'required|website',
            'position' => 'required',
        ], [
            'image.required' => __('Required to fill in'),
            'name.required' => __('Required to fill in'),
            'name.unique' => __('A partner with this name already exists'),
            'link.required' => __('Required to fill in'),
            'position.required' => __('Required to fill in'),
        ]);

        $item = new PartnersItems($request->all());
        $item->image = $request->file('image')->store('upload');
        $item->auditorium_ru = isset($request->auditorium_ru);
        $item->auditorium_en = isset($request->auditorium_en);
        $item->short_link = $item->generateShortLink();
        $item->save();

        flash()->overlay(__('A partner was successfully created'), ' ')->success();

        return Redirect::route('partners.admin');
    }

    public function editItemView(PartnersItems $item)
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $groups = PartnersGroups::get();

        return view('partners.edit-item', compact('groups', 'item'));
    }

    public function editItem(Request $request): ?RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $item = PartnersItems::findOrfail($request->id);

        $this->validate($request, [
            'image' => 'mimes:jpeg,png,jpg|max:2048',
            'link' => 'required|website',
            'name' => ['required', Rule::unique('partners_items')->ignore($item->name, 'name')],
            'position' => ['required', Rule::unique('partners_items')->ignore($item->position, 'position')],
        ], [
            'image.required' => __('Required to fill in'),
            'name.required' => __('Required to fill in'),
            'name.unique' => __('A partner with this name already exists'),
            'link.required' => __('Required to fill in'),
            'position.required' => __('Required to fill in'),
            'position.unique' => __('This position is occupied'),
        ]);


        $array = $request->all();

        if ($request->hasFile('image')) {
            if (file_exists(public_path('storage\\' . $item->image))) {
                unlink(public_path('storage\\' . $item->image));
            }
            $array['image'] = $request->file('image')->store('upload');
        }

        $array['auditorium_ru'] = $request->auditorium_ru === 'on';
        $array['auditorium_en'] = $request->auditorium_en === 'on';
        $item->update($array);

        flash()->overlay(__('A partner was successfully edited'), ' ')->success();
        return Redirect::route('partners.admin');
    }


    public function removeItem(Request $request): ?JsonResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $item = PartnersItems::findOrFail($request->id);
        $item->delete();

        return response()->json([], 200);
    }

    public function redirect(string $short_link)
    {
        $item = PartnersItems::where('short_link', '=', $short_link)->first();

        if (isset($item)) {
            header('Location: ' . $item->link);
            exit();
        }

        return abort(403, 'Link not found');
    }
}
