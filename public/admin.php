<?php
// 后台入口文件

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

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Shanghai');

require __DIR__ . '/../config/routes.php';

$response = Core\Router::dispatch();
if ($response instanceof Core\Response) {
    $response->send();
} else {
    Core\Response::json($response)->send();
}
