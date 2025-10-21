<?php
namespace Core;

use Closure;

/**
 * 路由类
 * - 支持 RESTful 路由
 * - 路由参数解析
 * - 中间件支持（基于责任链）
 * - 404 处理
 */
class Router
{
    /** @var array 路由表 */
    protected static array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    /**
     * 注册通用路由
     *
     * @param string $method HTTP 方法
     * @param string $pattern 路由规则，如 /apis/{id}
     * @param callable|array $handler 处理器，函数或 [Class, method]
     * @param array $middlewares 中间件类名数组
     */
    public static function add(string $method, string $pattern, $handler, array $middlewares = []): void
    {
        $method = strtoupper($method);
        self::$routes[$method][] = [
            'pattern' => $pattern,
            'regex' => self::compilePattern($pattern),
            'params' => self::extractParamNames($pattern),
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public static function get(string $pattern, $handler, array $middlewares = []): void
    {
        self::add('GET', $pattern, $handler, $middlewares);
    }

    public static function post(string $pattern, $handler, array $middlewares = []): void
    {
        self::add('POST', $pattern, $handler, $middlewares);
    }

    public static function put(string $pattern, $handler, array $middlewares = []): void
    {
        self::add('PUT', $pattern, $handler, $middlewares);
    }

    public static function patch(string $pattern, $handler, array $middlewares = []): void
    {
        self::add('PATCH', $pattern, $handler, $middlewares);
    }

    public static function delete(string $pattern, $handler, array $middlewares = []): void
    {
        self::add('DELETE', $pattern, $handler, $middlewares);
    }

    /**
     * 定义 RESTful 资源路由
     *
     * @param string $prefix 路由前缀，如 /apis
     * @param string $controller 控制器类
     * @param array $middlewares 中间件
     */
    public static function restful(string $prefix, string $controller, array $middlewares = []): void
    {
        $prefix = rtrim($prefix, '/');
        self::get($prefix, [$controller, 'index'], $middlewares);
        self::post($prefix, [$controller, 'store'], $middlewares);
        self::get($prefix . '/{id}', [$controller, 'show'], $middlewares);
        self::put($prefix . '/{id}', [$controller, 'update'], $middlewares);
        self::patch($prefix . '/{id}', [$controller, 'update'], $middlewares);
        self::delete($prefix . '/{id}', [$controller, 'destroy'], $middlewares);
    }

    /**
     * 分发请求
     */
    public static function dispatch(?Request $request = null)
    {
        $request = $request ?: Request::capture();
        $method = $request->getMethod();
        $path = $request->getPath();

        $routes = self::$routes[$method] ?? [];
        foreach ($routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = [];
                foreach ($route['params'] as $name) {
                    $params[$name] = $matches[$name] ?? null;
                }

                $handler = self::wrapHandler($route['handler']);

                // 构建中间件责任链
                $pipeline = array_reverse($route['middlewares']);
                $next = $handler;
                foreach ($pipeline as $mwClass) {
                    $next = self::makeMiddleware($mwClass, $next);
                }

                return $next($request, $params);
            }
        }

        // 未匹配路由，返回 404
        return Response::json(null, 404, 'Not Found', 404);
    }

    /**
     * 将路由规则编译为正则表达式
     */
    protected static function compilePattern(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . rtrim($regex, '/') . '$#';
    }

    /**
     * 提取参数名
     */
    protected static function extractParamNames(string $pattern): array
    {
        preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', $pattern, $m);
        return $m[1] ?? [];
    }

    /**
     * 包装处理器为统一的闭包
     */
    protected static function wrapHandler($handler): callable
    {
        return function (Request $request, array $params) use ($handler) {
            if (is_array($handler) && count($handler) === 2) {
                [$class, $method] = $handler;
                if (!class_exists($class)) {
                    return Response::json(null, 500, '控制器不存在: ' . $class, 500);
                }
                $instance = new $class;
                if (!method_exists($instance, $method)) {
                    return Response::json(null, 500, '方法不存在: ' . $class . '::' . $method, 500);
                }
                $result = $instance->$method($request, $params);
            } elseif (is_callable($handler)) {
                $result = call_user_func($handler, $request, $params);
            } else {
                return Response::json(null, 500, '无效的路由处理器', 500);
            }

            // 统一响应处理
            if ($result instanceof Response) {
                return $result;
            }
            return Response::json($result);
        };
    }

    /**
     * 实例化中间件并构造调用闭包
     */
    protected static function makeMiddleware(string $mwClass, callable $next): callable
    {
        return function (Request $request, array $params) use ($mwClass, $next) {
            if (!class_exists($mwClass)) {
                return Response::json(null, 500, '中间件不存在: ' . $mwClass, 500);
            }
            $mw = new $mwClass();
            if (!($mw instanceof \Core\Middleware)) {
                // 兼容：允许应用层中间件继承自 Core\Middleware 基类
            }
            return $mw->handle($request, $params, $next);
        };
    }
}
