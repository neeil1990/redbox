<?php

namespace App\Http\Controllers;

use App\ProjectRelevanceHistory;
use App\ProjectRelevanceHistoryTags;
use App\RelevanceTags;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectRelevanceHistoryTagsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $project = ProjectRelevanceHistory::where('id', '=', $request->projectId)->where('user_id', '=', Auth::id())->first();
        $tag = RelevanceTags::where('id', '=', $request->tagId)->where('user_id', '=', Auth::id())->first();

        if (isset($project) && isset($tag)) {
            $link = ProjectRelevanceHistoryTags::where('relevance_history_id', '=', $request->projectId)
                ->where('tags_id', '=', $request->tagId)->first();

            if ($link === null) {
                $link = new ProjectRelevanceHistoryTags();
                $link->relevance_history_id = $request->projectId;
                $link->tags_id = $request->tagId;
                $link->save();

                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => __('The label was successfully added to the project'),
                    'tag' => $tag,
                    'project' => $project
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'code' => 415,
                    'message' => __('You cant link a label') . " $tag->name " . __('to the project') . "$project->name " . __('again')
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'code' => 415,
            'message' => __('The project or label does not exist')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {

        $link = ProjectRelevanceHistoryTags::where('relevance_history_id', '=', $request->projectId)
            ->where('tags_id', '=', $request->tagId)->first();

        if (isset($link)) {
            $link->delete();
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => __('The label was successfully removed from the project')
            ]);
        }

        return response()->json([
            'success' => false,
            'code' => 415,
            'message' => __('Invalid request')
        ]);
    }
}
