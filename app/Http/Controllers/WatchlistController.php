<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AnimeController;
use App\Models\UserHiddenList;
use App\Models\WatchList;
use App\Models\Anime;
use App\Models\AnimeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        // アニメのタイトルまたはサブタイトルで検索キーワードを取得
        $keyword = $request->keyword;

        // タイトルにキーワードを含むアニメグループIDを取得
        $animeTitle = Anime::where('title', 'like', "%{$keyword}%")
            ->pluck('anime_group_id')
            ->unique();

        // サブタイトルにキーワードを含むアニメグループIDを取得
        $animeSubTitle = Anime::where('sub_title', 'like', "%{$keyword}%")
            ->pluck('anime_group_id')
            ->unique();

        // タイトルとサブタイトルの検索結果をまとめる
        $animeGroupIds = $animeTitle->merge($animeSubTitle);

        // アニメグループとログイン中のユーザーのウォッチリストを取得
        $anime_group_lists = AnimeGroup::with(
            [
                'animes.watchlists' => function ($query) {
                    // ログイン中のユーザーのウォッチリストを取得
                    return $query->where('user_id', Auth::user()->id);
                },
                // 非表示リストを取得
                'hiddenLists',
            ]
        )
            ->withCount('animes') // 各アニメグループのアニメ数をカウント
            ->whereIn('id', $animeGroupIds) // 検索キーワードに一致するアニメグループのみ選択
            ->doesntHave('hiddenLists') // 非表示リストに含まれないアニメグループのみ表示
            ->orderBy('created_at', 'desc') // 作成日時の降順で並び替え
            ->paginate(15); // ページネーションの設定（1ページあたり15件）

        $animeController = new AnimeController();
        foreach ($anime_group_lists as $anime_group_list) {
            // annict_idが存在する場合のみエピソード数を取得
            if ($anime_group_list->annict_id) {
                $anime_group_list->total_episodes = $animeController->annict_episode_count($anime_group_list->annict_id);
            } else {
                $anime_group_list->total_episodes = 0;
            }

            // 視聴済みのカウント
            $anime_group_list->watched_count = 0;
            foreach ($anime_group_list->animes as $anime) {
                // ステータスが1のウォッチリストをカウント
                $anime_group_list->watched_count += $anime->watchlists->where('status', 1)->count();
            }
        }

        return view('watch_lists.index', ['anime_group_lists' => $anime_group_lists]);
    }

    public function selectAnimeGroup()
    {
        // 全てのアニメグループを取得
        $animeGroups = AnimeGroup::all();

        // ログインしているユーザーの非表示リストを取得
        $localUserHiddenLists = UserHiddenList::whereUserId(Auth::user()->id)->select('anime_group_id')->get();

        // 非表示リストを初期化
        $userHiddenLists = [];

        // ユーザーが非表示にしたアニメグループIDを取得して、非表示リストに追加する
        foreach ($localUserHiddenLists as $localUserHiddenList) {
            $userHiddenLists[] = $localUserHiddenList->anime_group_id;
        }

        return view('watch_lists.select_anime', compact('animeGroups', 'userHiddenLists'));
    }

    public function create(Request $request)
    {
        // ログインしているユーザーのIDを取得し、アニメIDとメモを取得
        $watch_lists_notes = WatchList::whereUserId(Auth::user()->id)->pluck('notes', 'anime_id');

        // 指定されたアニメグループのアニメを取得
        $animes = Anime::whereAnimeGroupId($request->animeGroupId)->get();

        // ログイン中のユーザーのウォッチリストを取得
        $watch_lists = WatchList::whereUserId(Auth::user()->id)->pluck('anime_id')->toArray();

        // ログイン中のユーザーのステータス別に視聴済みと視聴中のアニメIDを取得
        $watch_lists_comp = WatchList::whereUserId(Auth::user()->id)->whereStatus(1)->pluck('anime_id')->toArray();
        $watch_lists_in = WatchList::whereUserId(Auth::user()->id)->whereStatus(2)->pluck('anime_id')->toArray();

        return view('watch_lists.create', compact('animes', 'watch_lists', 'watch_lists_comp', 'watch_lists_in', 'watch_lists_notes'));
    }

    public function store(Request $request)
    {
        // バリデーションのエラーメッセージを設定
        $request->validate([
            'anime_check_lists' => 'required',
        ], [
            'anime_check_lists.required' => 'アニメチェックリストを選択してください。'
        ]);

        // チェックされたアニメをウォッチリストに登録
        foreach ($request->anime_check_lists as $anime_check_list) {
            // 既にウォッチリストに登録されている場合はエラーをスローしてメッセージを表示
            if (WatchList::whereAnime_id($anime_check_list)->whereUserId(Auth::user()->id)->get()->count() >= 1) {
                throw ValidationException::withMessages([
                    'anime_check_lists' => 'すでに登録されています。',
                ]);
            };

            // 新しいウォッチリストにアニメを追加
            $watch_list = new WatchList();
            $watch_list->anime_id = $anime_check_list;
            $watch_list->user_id = Auth::user()->id;
            $watch_list->save();
        }

        return redirect()->route('watch_list.index')->with('flash_message', '登録が完了しました。');
    }

    public function edit(WatchList $watch_list)
    {
        // ログイン中のユーザーでない場合、リダイレクト
        if ($watch_list->user_id !== Auth::id()) {
            return redirect()->route('watch_list.index')->with('error_message', '不正なアクセスです。');
        }

        $animes = Anime::all();

        return view('watch_lists.edit', compact('watch_list', 'animes'));
    }

    public function update(Request $request, Watchlist $watch_list)
    {
        // ログイン中のユーザーでない場合、リダイレクト
        if ($watch_list->user_id !== Auth::id()) {
            return redirect()->route('watch_list.index')->with('error_message', '不正なアクセスです。');
        }

        // バリデーションの設定
        $request->validate([
            'anime_id' => 'required',
        ]);

        // 既にウォッチリストに登録されているアニメの場合はエラーメッセージを返す
        if (WatchList::whereAnime_id($request->anime_id)->whereUser_id(Auth::user()->id)->get()->count() >= 1) {
            return back()->withInput()->withErrors('すでに登録されています。');
        };

        // ウォッチリストのアニメIDを更新
        $watch_list->anime_id = $request->anime_id;
        $watch_list->save();

        return redirect()->route('watch_list.index')->with('flash_message', '登録を編集しました。');
    }

    public function destroy(Watchlist $watch_list)
    {
        // ログイン中のユーザーでない場合、リダイレクト
        if ($watch_list->user_id !== Auth::id()) {
            return redirect()->route('watch_list.index')->with('error_message', '不正なアクセスです。');
        }

        $watch_list->delete();

        return redirect()->route('watch_list.index')->with('flash_message', '登録を削除しました。');
    }
}