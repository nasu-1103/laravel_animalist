<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WatchList;
use Illuminate\Http\Request;

class WatchlistAPIController extends Controller
{
    public function setStatus(Request $request)
    {
        // ステータスが1または2の場合
        if ($request->status === "1" || $request->status === "2") {
            // 指定されたユーザーIDとアニメIDが一致するウォッチリストを取得
            $watchLists = WatchList::whereUserId($request->user_id)->whereAnimeId($request->anime_id)->get();

            // 既にウォッチリストが存在する場合は削除
            if ($watchLists->count() >= 1) {
                $watchLists[0]->delete();
            }

            // 新しいウォッチリストを作成
            WatchList::create([
                'anime_id' => $request->anime_id,
                'user_id' => $request->user_id,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);
            // ウォッチリストが作成されたら結果を返す
            return ['result' => 'insert'];
        } else {
            // ステータスが1または2でない場合は、ウォッチリストを削除
            WatchList::whereUserId($request->user_id)
                ->whereAnimeId($request->anime_id)
                ->delete();
            // ウォッチリストが削除されたら結果を返す
            return ['result' => 'delete'];
        }
        // エラーが発生したらエラーを返す
        return ['result' => 'error'];
    }

    public function setNotes(Request $request)
    {
        // ユーザーIDを元にユーザーを検索
        $user = User::find($request->user_id);

        // ユーザーが見つかった場合の処理
        if ($user) {
            // メモとユーザー情報を保存
            $user->notes = $request->notes;
            $user->save();
            // メモが更新されたら結果を返す
            return ['result' => 'update'];
        } else {
            // エラーが発生したらエラーを返す
            return ['result' => 'error'];
        }
    }

    public function changeStatus(Request $request)
    {
        // アニメIDを取得
        $animeId = $request->input('anime_id');

        // アニメIDに関連するウォッチリストのエピソードを全て取得
        $watchlists = WatchList::where('anime_id', $animeId)->get();

        // ウォッチリスト内に、未視聴または視聴中のエピソードがあるかチェック
        $hasUnwatchedOrWatching = $watchlists->contains(function ($watchlist) {
            // 視聴済み以外のステータスをチェック
            return $watchlist->status != 1;
        });

        // 👑を表示する条件を設定
        if ($hasUnwatchedOrWatching) {
            // 未視聴または視聴中のエピソードがある場合、👑は非表示
            return response()->json(['👑' => false]);
        } else {
            // 全てのエピソードが視聴済みの場合、👑を表示
            return response()->json(['👑' => true]);
        }
    }
}
