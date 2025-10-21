<?php
namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Logger;

/**
 * 日志记录中间件
 */
class LogMiddleware extends Middleware
{
    public function handle(Request $request, array $params, callable $next)
    {
        $start = microtime(true);
        $response = $next($request, $params);
        $cost = (int)((microtime(true) - $start) * 1000);
        Logger::info('HTTP ' . $request->getMethod() . ' ' . $request->getPath(), [
            'ip' => $request->ip(),
            'cost_ms' => $cost,
        ]);
        return $response;
    }
}
