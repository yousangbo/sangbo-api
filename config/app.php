<?php
// 系统基础配置
return [
    // 应用名称
    'name' => 'Sangbo API 管理系统',

    // 应用环境（production/staging/development）
    'env' => 'development',

    // 调试模式
    'debug' => true,

    // 时区设置
    'timezone' => 'Asia/Shanghai',

    // 加密密钥（用于加解密、CSRF等）
    'key' => 'ChangeMeToA32CharsRandomKey1234',

    // 日志目录
    'log_path' => __DIR__ . '/../storage/logs',

    // 缓存目录
    'cache_path' => __DIR__ . '/../storage/cache',

    // 上传目录
    'upload_path' => __DIR__ . '/../storage/uploads',
];
