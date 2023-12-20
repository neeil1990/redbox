<?php

namespace App\Http\Controllers;

use App\News;
use App\NewsComments;
use App\NewsLikes;
use App\NewsNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use JavaScript;


class NewsController extends Controller
{
    /**
     * @return array|Application|Factory|View|mixed
     */
    public function index()
    {
        $notification = NewsNotification::firstOrNew(['user_id' => Auth::id()]);
        $notification->last_check = Carbon::now();
        $notification->save();
        $news = News::all();
        $news = $news->sortByDesc('created_at');
        $admin = NewsController::isUserAdmin();
        if ($admin) {
            JavaScript::put([
                'role' => __('Admin'),
            ]);
        } else {
            JavaScript::put([
                'role' => __('User'),
            ]);
        }

        return view('news.index', compact('news', 'admin'));
    }

    /**
     * @return array|Application|Factory|View|mixed
     */
    public function createView()
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        return view('news.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (!User::isUserAdmin()) {
            return abort(403);
        }

        $news = $request->all();
        $news['user_id'] = Auth::id();
        $news = new News($news);
        $news->save();

        flash()->overlay(__('The news was successfully created'), ' ')->success();

        return Redirect::back();
    }


    public function remove(Request $request)
    {
        News::destroy($request->id);

        return response([], 200);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function storeComment(Request $request)
    {
        $request = $request->all();
        $request['user_id'] = Auth::id();
        $comment = new NewsComments($request);
        $comment->save();

        return response([
            'commentId' => $comment->id,
            'userName' => Auth::user()->name,
            'createdAt' => 'Только что',
            'avatar' => Auth::user()->image,
            'comment' => $comment->comment
        ], 200);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function removeComment(Request $request)
    {
        NewsComments::destroy($request->id);

        return response([], 200);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function likeNews(Request $request)
    {
        $userId = Auth::id();
        $like = NewsLikes::where(['user_id' => $userId, 'news_id' => $request->id])->first();
        $news = News::where('id', '=', $request->id)->first();

        if (isset($like)) {
            $response = 'unlike';
            $like->delete();
            $news->number_of_likes--;
        } else {
            $response = 'like';
            $like = new NewsLikes(['user_id' => $userId, 'news_id' => $request->id]);
            $news->number_of_likes++;
            $like->save();
        }
        $news->save();

        return response([
            $response
        ], 200);
    }

    /**
     * @param $id
     * @return array|false|Application|Factory|View|mixed
     */
    public function editNewsView($id)
    {
        $news = News::where('id', '=', $id)->first();

        return view('news.edit', compact('news'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function editNews(Request $request): RedirectResponse
    {
        News::where('id', '=', $request->id)->update([
            'content' => $request['content']
        ]);

        return Redirect::route('news');
    }

    public function editComment(Request $request)
    {
        NewsComments::where('id', '=', $request->id)->update([
            'comment' => $request->comment
        ]);

        return response([], 200);
    }

    /**
     * @return bool
     */
    public static function isUserAdmin(): bool
    {
        $roles = (array)Auth::user()->role;
        if (in_array(1, $roles["\x00*\x00items"])) {
            $admin = true;
        } else {
            $admin = false;
        }

        return $admin;
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public static function calculateCountNewNews()
    {
        $notification = NewsNotification::where('user_id', '=', Auth::id())->first();
        if (isset($notification)) {
            $count = News::where('created_at', '>=', $notification->last_check)->get()->count();
        } else {
            $count = News::all()->count();
        }

        return response([
            'count' => $count
        ], 200);
    }
}
