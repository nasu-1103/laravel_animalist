<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
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
}