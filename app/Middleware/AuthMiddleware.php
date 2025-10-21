<?php
namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;

/**
 * 权限验证中间件（示例）
 * 实际项目中应结合登录态与权限系统实现
 */
class AuthMiddleware extends Middleware
{
    public function handle(Request $request, array $params, callable $next)
    {
        // 示例：校验自定义管理端 Token（仅示例）
        $token = $request->header('x-admin-token');
        if (!$token) {
            return Response::json(null, 401, '未授权', 401);
        }
        return $next($request, $params);
    }
}
