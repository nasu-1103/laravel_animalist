<?php

namespace App\Http\Controllers;

use App\Models\AnimeGroup;
use App\Models\UserHiddenList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class AnimeGroupController extends Controller
{
    public function index(Request $request)
    {
        //　検索キーワードを取得
        $keyword = $request->keyword;

        if ($keyword) {
            // キーワードを含むアニメグループを取得し、ページネーションを適応
            $animeGroups = AnimeGroup::where('name', 'like', "%{$keyword}%")->paginate(15);

            // 検索結果の総数を取得
            $total_count = count($animeGroups);

            // ページネーションに検索キーワードを保持
            $animeGroups->appends(['keyword' => $keyword]);
        } else {
            //キーワードがない場合は全てのアニメグループを15件ずつ取得し、ページネーションの設定
            $animeGroups = AnimeGroup::paginate(15);

            // 取得したアニメグループの件数をカウント
            $total_count = count($animeGroups);
        }

        return view('anime_groups.index', compact('animeGroups', 'total_count', 'keyword'));
    }

    public function create()
    {
        return view('anime_groups.create');
    }

    public function annict_search()
    {
        return view('anime_groups.annict_search');
    }

    public function annict_list(Request $request)
    {
        // 検索ワードにバリデーションを設定
        $request->validate([
            'search_word' => ['required', 'string', 'max:255'],
        ]);

        // Annict APIからアニメデータを取得
        $token = env('ANNICT_TOKEN');
        $url = "https://api.annict.com/v1/works?filter_title=" .
            $request->search_word . "&sort_season=asc&page=" . $request->page;
        $res = Http::withToken($token)->get($url);

        // Annict APIからアニメのタイトルを取得
        $works = $res->json()['works'];

        // Annict APIから総件数を取得
        $count = $res->json()['total_count'];

        return view('anime_groups.annict_list', compact('works', 'count'));
    }

    public function store(Request $request)
    {
        // 入力バリデーションを設定
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        // 同じ名前のアニメグループが既に存在する場合はエラーを返す
        if (AnimeGroup::whereName($request->name)->get()->count() >= 1) {
            return back()->withInput()->withErrors('すでに登録されています。');
        };

        // アニメグループを作成
        AnimeGroup::create([
            'name' => $request->name,
        ]);

        return redirect()->route('anime_groups.index')->with('flash_message', '登録が完了しました。');
    }

    public function annict_store(Request $request)
    {
        // バリデーションの設定
        $request->validate([
            'idTitle' => ['required', 'string'],
        ]);

        // 入力をAnnict IDとタイトル名と分割
        [$annict_id, $name] = explode(",", $request->idTitle);

        // 同じAnnict IDのアニメグループが存在する場合はエラーを返す
        if (AnimeGroup::whereAnnictId($annict_id)->get()->count() >= 1) {
            return back()->withInput()->withErrors('すでに登録されています。');
        };

        // 新しいアニメグループを保存
        AnimeGroup::create([
            'name' => $name,
            'annict_id' => $annict_id
        ]);

        return redirect()->route('anime_groups.index')->with('flash_message', '登録が完了しました。');
    }

    public function edit(AnimeGroup $animeGroup)
    {
        // 管理者以外のアクセスを制限
        if (Gate::denies('admin')) {
            return redirect()->route('anime_groups.index')->with('error_message', '不正なアクセスです。');
        }

        return view('anime_groups.edit', compact('animeGroup'));
    }

    public function update(Request $request, AnimeGroup $animeGroup)
    {
        // 管理者以外のアクセスを制限
        if (Gate::denies('admin')) {
            return redirect()->route('anime_groups.index')->with('error_message', '不正なアクセスです。');
        }

        // バリデーションの設定
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        // 同じ名前のアニメグループが存在する場合はエラーを返す
        if (AnimeGroup::whereName($request->name)->get()->count() >= 1) {
            return back()->withInput()->withErrors('すでに登録されています。');
        };

        // アニメグループの名前を更新
        $animeGroup->name = $request->name;
        $animeGroup->save();

        return redirect()->route('anime_groups.index')->with('flash_message', '登録を編集しました。');
    }

    public function show(Request $request, AnimeGroup $animeGroup)
    {
        // Annict IDが存在する場合はAPIからエピソードデータを取得
        if ($animeGroup->annict_id) {
            $token = env('ANNICT_TOKEN');
            $url = "https://api.annict.com/v1/episodes?filter_work_id=" .
                $animeGroup->annict_id . "&sort_sort_number=asc&page=1";
            $res = Http::withToken($token)->get($url);
            $episodes = $res->json()['episodes'];

            // エピソードのnumberがnullの場合、numberからnumber_textに設定
            foreach ($episodes as &$episode) {
                $episode['number'] ??=
                    str_replace(['第', '話', 'OVA'], '', $episode['number_text']);
            };
        } else {
            // Annict IDが存在しない場合、エピソードの情報は取得できない
            $episodes = [];
        }

        return view('anime_groups.show', compact('animeGroup', 'episodes'));
    }

    public function destroy(AnimeGroup $animeGroup)
    {
        // 管理者以外のアクセスを制限
        if (Gate::denies('admin')) {
            return redirect()->route('anime_groups.index')->with('error_message', '不正なアクセスです。');
        }

        $animeGroup->delete();

        return redirect()->route('anime_groups.index')->with('flash_message', '登録を削除しました。');
    }

    public function setting()
    {
        // 全てのアニメグループを取得
        $animeGroups = AnimeGroup::all();

        // ログインしているユーザー非表示リストを取得
        $localUserHiddenLists = UserHiddenList::whereUserId(Auth::user()->id)->select('anime_group_id')->get();
        $userHiddenLists = [];
        foreach ($localUserHiddenLists as $localUserHiddenList) {
            $userHiddenLists[] = $localUserHiddenList->anime_group_id;
        }

        return view('watch_lists.setting', compact('animeGroups', 'userHiddenLists'));
    }

    public function settingAdd(Request $request)
    {
        // 非表示リストにアニメグループを追加
        UserHiddenList::create([
            'anime_group_id' => $request->anime_group_id,
            'user_id' => Auth::user()->id
        ]);

        return redirect()->route('watch_list.index')->with('flash_message', 'リストを登録しました。');
    }

    public function settingDelete(Request $request)
    {
        // ユーザーの非表示リストからアニメグループを削除
        UserHiddenList::whereAnimeGroupId($request->anime_group_id)->whereUserId(Auth::user()->id)->delete();

        return redirect()->route('watch_list.index')->with('flash_message', 'リストを削除しました。');
    }
}