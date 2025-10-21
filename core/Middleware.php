<?php
namespace Core;

/**
 * 中间件基类
 * 应用中间件需继承本类并实现 handle 方法
 */
abstract class Middleware
{
    /**
     * 处理中间件逻辑
     *
     * @param Request $request 请求对象
     * @param array $params 路由参数
     * @param callable $next 下一个处理器
     * @return mixed 返回 Response 或 任意数据
     */
    public function handle(Request $request, array $params, callable $next)
    {
        return $next($request, $params);
    }
}
