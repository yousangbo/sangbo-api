<?php
// 前端入口文件

// 自动加载（优先 Composer，其次内置 PSR-4 自动加载）
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefixes = [
            'Core\\' => __DIR__ . '/../core/',
            'App\\'  => __DIR__ . '/../app/',
        ];
        foreach ($prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($class, $prefix, $len) !== 0) continue;
            $relative = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
        }
        return false;
    });
}

// 载入配置
$appConfig = require __DIR__ . '/../config/app.php';
// 时区
date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Shanghai');

// 注册路由
require __DIR__ . '/../config/routes.php';

// 分发请求
$response = Core\Router::dispatch();
if ($response instanceof Core\Response) {
    $response->send();
} else {
    Core\Response::json($response)->send();
}
