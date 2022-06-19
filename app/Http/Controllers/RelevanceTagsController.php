<?php

namespace App\Http\Controllers;

use App\RelevanceTags;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelevanceTagsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $tags = RelevanceTags::where('user_id', '=', Auth::id())->get();

        return response()->json([
            'tags' => $tags
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $tag = RelevanceTags::where('name', '=', $request->name)
            ->where('user_id', '=', Auth::id())->first();

        if (!isset($tag)) {
            $newTag = new RelevanceTags();
            $newTag->name = $request->name;
            $newTag->color = $request->color;
            $newTag->user_id = Auth::id();
            $newTag->save();

            return response()->json([
                'success' => true,
                'code' => 201,
                'tag' => $newTag,
                'message' => 'Метка успешно создана'
            ]);
        }

        return response()->json([
            'success' => false,
            'code' => 415,
            'message' => 'Такая метка уже существует'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request): JsonResponse
    {
        $tag = RelevanceTags::where('id', '=', $request->tagId)->where('user_id', '=', Auth::id())->first();
        if (isset($tag)) {
            $tag->delete();
            return response()->json([
                'success' => true,
                'code' => 200
            ]);
        }

        return response()->json([
            'success' => false,
            'code' => 415
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        $tag = RelevanceTags::where('id', '=', $request->tagId)->where('user_id', '=', Auth::id())->first();
        if (isset($tag)) {
            if (isset($request->name)) {
                $tag->name = $request->name;
            } else {
                $tag->color = $request->color;
            }

            $tag->save();

            return response()->json([
                'success' => false,
                'message' => 'Метка успешно изменена',
                'code' => 200
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Метка не существует',
            'code' => 415
        ]);
    }
}
