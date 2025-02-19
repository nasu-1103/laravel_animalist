<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // 管理者以外のアクセスを制限
    protected function authorizeAdmin()
    {
        if (Gate::denies('admin')) {
            abort(403, '不正なアクセスです。');
        }
    }

    // ログイン中のユーザーでない場合、リダイレクト
    protected function authorizeUser($watch_list)
    {
        if ($watch_list->user_id !== Auth::id()) {
            return redirect()->route('watch_list.index')->with('error_message', '不正なアクセスです。');
        }
    }
}