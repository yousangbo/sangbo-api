<?php
namespace App\Controllers\Admin;

use Core\Request;
use Core\Response;

/**
 * 后台控制台控制器
 */
class DashboardController
{
    public function index(Request $request, array $params)
    {
        return Response::json([
            'admin' => true,
            'message' => '欢迎进入后台',
        ]);
    }
}
