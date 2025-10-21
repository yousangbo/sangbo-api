<?php
namespace App\Controllers\Frontend;

use Core\Request;
use Core\Response;

/**
 * 前端首页控制器
 */
class HomeController
{
    public function index(Request $request, array $params)
    {
        return Response::json([
            'app' => 'Sangbo API 管理系统',
            'time' => date('Y-m-d H:i:s'),
        ]);
    }

    public function ping(Request $request, array $params)
    {
        return Response::json(['pong' => true]);
    }
}
