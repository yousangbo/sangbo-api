<?php
namespace Core;

/**
 * 日志类
 * - 支持异步（缓冲）写入
 * - 日志级别（INFO/WARNING/ERROR）
 * - 按日期分割日志文件
 */
class Logger
{
    protected static ?string $logPath = null;
    protected static array $queue = [];
    protected static bool $registered = false;

    /** 初始化日志目录 */
    protected static function init(): void
    {
        if (self::$logPath === null) {
            $config = require __DIR__ . '/../config/app.php';
            self::$logPath = rtrim($config['log_path'] ?? (__DIR__ . '/../storage/logs'), '/');
            if (!is_dir(self::$logPath)) @mkdir(self::$logPath, 0777, true);
        }
        if (!self::$registered) {
            self::$registered = true;
            register_shutdown_function([self::class, 'flush']);
        }
    }

    /** 通用日志写入 */
    public static function log(string $level, string $message, array $context = [], bool $async = true): void
    {
        self::init();
        $time = date('Y-m-d H:i:s');
        $line = sprintf('[%s] %s: %s %s', $time, strtoupper($level), $message, $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '');
        if ($async) {
            self::$queue[] = $line;
        } else {
            self::writeLine($line);
        }
    }

    public static function info(string $message, array $context = [], bool $async = true): void
    {
        self::log('INFO', $message, $context, $async);
    }

    public static function warning(string $message, array $context = [], bool $async = true): void
    {
        self::log('WARNING', $message, $context, $async);
    }

    public static function error(string $message, array $context = [], bool $async = false): void
    {
        self::log('ERROR', $message, $context, $async);
    }

    /** 将缓冲区写入文件 */
    public static function flush(): void
    {
        if (empty(self::$queue)) return;
        foreach (self::$queue as $line) {
            self::writeLine($line);
        }
        self::$queue = [];
    }

    /** 实际写文件 */
    protected static function writeLine(string $line): void
    {
        $file = self::$logPath . '/' . date('Y-m-d') . '.log';
        @file_put_contents($file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
