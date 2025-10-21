<?php
namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;

/**
 * CSRF 验证中间件（示例）
 */
class CsrfMiddleware extends Middleware
{
    public function handle(Request $request, array $params, callable $next)
    {
        // 仅对非 GET 方法进行校验
        if (!in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            $token = $request->header('x-csrf-token');
            // 示例：未提供则拒绝
            if (!$token) {
                return Response::json(null, 419, 'CSRF 校验失败', 419);
            }
        }
        return $next($request, $params);
    }
}
