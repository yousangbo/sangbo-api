<?php
namespace Core;

/**
 * 缓存类（文件缓存实现，预留 Redis 接口）
 */
class Cache
{
    protected string $path;

    public function __construct(?string $path = null)
    {
        $config = require __DIR__ . '/../config/app.php';
        $this->path = $path ?: rtrim($config['cache_path'] ?? (__DIR__ . '/../storage/cache'), '/');
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0777, true);
        }
    }

    /** 生成缓存文件路径 */
    protected function file(string $key): string
    {
        $hash = md5($key);
        return $this->path . '/' . $hash . '.cache.php';
    }

    /** 写入缓存 */
    public function set(string $key, $value, int $ttl = 3600): bool
    {
        $data = [
            'expire' => time() + $ttl,
            'value' => $value,
        ];
        $content = '<?php return ' . var_export($data, true) . ';';
        return (bool)file_put_contents($this->file($key), $content, LOCK_EX);
    }

    /** 读取缓存 */
    public function get(string $key, $default = null)
    {
        $file = $this->file($key);
        if (!is_file($file)) return $default;
        $data = require $file;
        if (!is_array($data) || ($data['expire'] ?? 0) < time()) {
            @unlink($file);
            return $default;
        }
        return $data['value'] ?? $default;
    }

    /** 删除缓存 */
    public function delete(string $key): bool
    {
        $file = $this->file($key);
        return is_file($file) ? @unlink($file) : true;
    }

    /** 清空缓存目录 */
    public function clear(): void
    {
        foreach (glob($this->path . '/*.cache.php') as $file) {
            @unlink($file);
        }
    }
}
