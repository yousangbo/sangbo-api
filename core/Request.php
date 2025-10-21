<?php
namespace Core;

/**
 * 请求类
 * - 获取 GET/POST/PUT/DELETE 参数
 * - 文件上传处理
 * - 请求头获取
 * - IP 地址获取
 */
class Request
{
    /** 原始方法 */
    protected string $method;
    /** 原始路径 */
    protected string $path;
    /** 查询参数 */
    protected array $query;
    /** 请求体参数（表单或 JSON） */
    protected array $body;
    /** 上传文件 */
    protected array $files;
    /** 请求头 */
    protected array $headers;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->query = $_GET ?? [];
        $this->files = $_FILES ?? [];
        $this->headers = $this->parseHeaders();
        $this->body = $this->parseBody();
    }

    /** 捕获请求（静态工厂） */
    public static function capture(): self
    {
        return new self();
    }

    /** 获取 HTTP 方法 */
    public function getMethod(): string
    {
        return $this->method;
    }

    /** 获取路径（不含查询串） */
    public function getPath(): string
    {
        return rtrim($this->path, '/') ?: '/';
    }

    /** 获取查询参数 */
    public function query(string $key = null, $default = null)
    {
        if ($key === null) return $this->query;
        return $this->query[$key] ?? $default;
    }

    /** 获取请求体参数（适配 JSON 或 表单） */
    public function input(string $key = null, $default = null)
    {
        if ($key === null) return $this->body;
        return $this->body[$key] ?? $default;
    }

    /** 获取某个参数（优先 body，其次 query） */
    public function get(string $key, $default = null)
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    /** 获取所有上传文件 */
    public function files(): array
    {
        return $this->files;
    }

    /** 获取请求头 */
    public function header(string $key = null, $default = null)
    {
        if ($key === null) return $this->headers;
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    /** 客户端 IP */
    public function ip(): string
    {
        $keys = [
            'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'
        ];
        foreach ($keys as $k) {
            $ip = $_SERVER[$k] ?? '';
            if ($ip) {
                // 处理多级代理
                $ip = explode(',', $ip)[0];
                return trim($ip);
            }
        }
        return '0.0.0.0';
    }

    /**
     * 解析请求头
     */
    protected function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        if (function_exists('getallheaders')) {
            foreach ((array)getallheaders() as $k => $v) {
                $headers[strtolower($k)] = $v;
            }
        }
        return $headers;
    }

    /**
     * 解析请求体
     */
    protected function parseBody(): array
    {
        $body = [];
        $contentType = strtolower((string)$this->header('content-type', ''));
        if (in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (strpos($contentType, 'application/json') !== false) {
                $raw = file_get_contents('php://input');
                $data = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    $body = $data;
                }
            } else {
                // 默认表单提交
                $body = $_POST ?? [];
                // 对于 PUT/PATCH/DELETE 且 content-type 为 x-www-form-urlencoded
                if ($this->method !== 'POST' && empty($body)) {
                    parse_str(file_get_contents('php://input'), $parsed);
                    if (is_array($parsed)) $body = $parsed;
                }
            }
        }
        return $body;
    }
}
