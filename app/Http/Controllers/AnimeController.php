<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\AnimeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnimeController extends Controller
{
    public function index(Request $request)
    {
        // 検索キーワードを取得
        $keyword = $request->keyword;

        if ($keyword) {
            // キーワードがタイトルまたはサブタイトルに含まれるアニメを検索し、ページネーションを適用
            $animes = Anime::where('title', 'like', "%{$keyword}%")
                ->orWhere('sub_title', 'like', "%{$keyword}%")->paginate(15);

            // 検索結果の総数を取得
            $total_count = $animes->total();

            // ページネーションに検索キーワードを保持
            $animes->appends(['keyword' => $keyword]);
        } else {
            // キーワードがない場合は全てのアニメを取得し、ページネーションを設定
            $animes = Anime::paginate(15);

            // 全アニメの数を取得
            $total_count = count($animes);
        }

        return view('animes.index', compact('animes', 'total_count', 'keyword'));
    }

    public function annict_search()
    {
        // Annict IDが存在するアニメグループを取得
        $animeGroups = AnimeGroup::whereNotNull('annict_id')->get();

        return view('animes.annict_search', compact('animeGroups'));
    }

    public function annict_list(Request $request)
    {
        // Annict IDのバリデーション
        $request->validate([
            'annict_id' => ['required', 'string', 'max:255'],
        ]);

        // Annict APIからエピソードを取得
        $anime_id = AnimeGroup::whereAnnictId($request->annict_id)->first()->id;
        $token = env('ANNICT_TOKEN');
        $url = "https://api.annict.com/v1/episodes?filter_work_id=" .
            $request->annict_id . "&sort_sort_number=asc&per_page=50&page=" . $request->page;
        $res = Http::withToken($token)->get($url);

        // エピソードデータを取得
        $episodes = $res->json()['episodes'];

        // 総エピソード数を取得
        $count = $res->json()['total_count'];

        // エピソードnumberがnullの場合、number_textからnumberに設定
        foreach ($episodes as &$episode) {
            $episode['number'] ??=
                str_replace(['第', '話', 'OVA'], '', $episode['number_text']);
        };

        return view('animes.annict_list', compact('episodes', 'count', 'anime_id'));
    }

    /**
     * 指定されたアニメグループIDとエピソード番号のデータが存在する場合、その情報を確認する
     * 
     * @param string|int $episodeNumber エピソード番号（文字列または整数）
     * @param int $animeGroupId アニメグループID
     * @return bool 指定された組み合わせが存在する場合は true、それ以外は false
     */
    public function episode_exists(string|int $episodeNumber, int $animeGroupId): bool
    {
        // エピソード番号が文字列の場合、整数に変換
        if (!is_int($episodeNumber)) {
            $episodeNumber = (int) $episodeNumber;
        }

        // アニメグループIDが文字列の場合、整数に変換
        if (!is_int($animeGroupId)) {
            $animeGroupId = (int) $animeGroupId;
        }

        // 指定されたエピソード番号とアニメグループIDで存在確認
        $exists = Anime::where('anime_group_id', $animeGroupId)
            ->where('episode', $episodeNumber)
            ->exists();

        return $exists;
    }

    public function annict_store(Request $request)
    {
        // バリデーションの設定
        $request->validate([
            'episode' => ['required', 'string'],
            'anime_id' => ['required'],
        ]);

        // エピソード番号とサブタイトルを分割
        [$number, $subTitle] = explode(",", $request->episode);

        // エピソードが既に登録されているか確認
        $exists = $this->episode_exists($number, $request->anime_id);

        // 登録済みの場合はエラーメッセージを表示して戻る
        if ($exists) {
            return back()->withInput()->withErrors('すでに登録されています。');
        }

        // 新しいアニメのエピソードのデータを作成
        Anime::create([
            'anime_group_id' => $request->anime_id,
            'title' => AnimeGroup::whereId($request->anime_id)->first()->name,
            'episode' => $number,
            'sub_title' => $subTitle,
        ]);

        return redirect()->route('animes.index')->with('flash_message', '登録が完了しました。');
    }

    public function annict_episode_count($annictId, $page = 1)
    {
        // Annict APIからエピソードの総数を取得
        $token = env('ANNICT_TOKEN');
        $url = "https://api.annict.com/v1/episodes?filter_work_id=" . $annictId . "&sort_sort_number=asc&page=" . $page;
        $res = Http::withToken($token)->get($url);

        // 総エピソード数を取得
        $totalCount = $res->json()['total_count'];

        // 総エピソード数が0の場合は1に修正
        return $totalCount > 0 ? $totalCount : 1;
    }

    public function create()
    {
        return view('animes.create');
    }


    public function store(Request $request)
    {
        // バリデーションの設定
        $request->validate([
            'episode' => ['required', 'integer'],
            'sub_title' => ['required'],
            'animeGroup' => ['required', 'integer'],
        ]);

        // 指定されたエピソードが存在するか確認
        $exists = $this->episode_exists($request->episode, $request->animeGroup);

        // 登録済みの場合はエラーメッセージを表示して戻る
        if ($exists) {
            return back()->withInput()->withErrors('すでに登録されています。');
        }

        // 新しいアニメのデータを保存
        $anime = new Anime();
        $anime->title = AnimeGroup::find($request->animeGroup)->name;
        $anime->episode = $request->episode;
        $anime->sub_title = $request->sub_title;
        $anime->anime_group_id = $request->animeGroup;
        $anime->save();

        return redirect()->route('animes.index')->with('flash_message', '登録が完了しました。');
    }

    public function show(Anime $anime)
    {
        return view('animes.show', compact('anime'));
    }

    public function update(Request $request, Anime $anime)
    {
        // 管理者以外のアクセスを制限
        $this->authorizeAdmin();

        // バリデーションの設定
        $request->validate([
            'title' => ['required'],
            'episode' => ['required', 'integer'],
            'sub_title' => ['required'],
        ]);

        // 同じタイトル、エピソード番号、サブタイトルが既に存在する場合はエラーを返す
        if (
            Anime::whereTitle($request->title)
            ->whereEpisode($request->episode)
            ->whereSubTitle($request->sub_title)
            ->get()->count() >= 1
        ) {

            return back()->withInput()->withErrors('すでに登録されています。');
        };

        // アニメデータを更新
        $anime->title = $request->title;
        $anime->episode = $request->episode;
        $anime->sub_title = $request->sub_title;
        $anime->save();

        return redirect()->route('animes.index', $anime)->with('flash_message', '登録を編集しました。');
    }

    public function edit(Anime $anime)
    {
        // 管理者以外のアクセスを制限
        $this->authorizeAdmin();

        return view('animes.edit', compact('anime'));
    }

    public function destroy(Anime $anime)
    {
        // 管理者以外のアクセスを制限
        $this->authorizeAdmin();

        $anime->delete();

        return redirect()->route('animes.index')->with('flash_message', '登録を削除しました。');
    }
}