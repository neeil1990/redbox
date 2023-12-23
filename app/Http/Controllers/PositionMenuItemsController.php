<?php

namespace App\Http\Controllers;

use App\MenuItemsPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionMenuItemsController extends Controller
{
    public function index()
    {
        $items = MenuItemsPosition::sortMenu();
        return view('positions.index', ['items' => $items]);
    }

    public function edit(Request $request): JsonResponse
    {
        $config = MenuItemsPosition::firstOrNew(['user_id' => Auth::id()]);
        $config->positions = $request->input('menuItems');
        $config->save();

        return response()->json([], 201);
    }

    public function remove(Request $request): JsonResponse
    {
        MenuItemsPosition::where('user_id', '=', Auth::id())->delete();

        return response()->json([], 200);
    }
}
