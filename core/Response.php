<?php
namespace Core;

/**
 * 响应类
 * - JSON 格式化输出
 * - HTTP 状态码设置
 * - 统一响应格式 {code, message, data}
 */
class Response
{
    protected int $httpCode;
    protected int $code;
    protected string $message;
    protected $data;
    protected array $headers = [];

    public function __construct($data = null, int $code = 0, string $message = 'success', int $httpCode = 200, array $headers = [])
    {
        $this->data = $data;
        $this->code = $code;
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->headers = $headers;
    }

    /**
     * 快捷创建 JSON 响应
     */
    public static function json($data = null, int $code = 0, string $message = 'success', int $httpCode = 200, array $headers = []): self
    {
        return new self($data, $code, $message, $httpCode, $headers);
    }

    /** 设置响应头 */
    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /** 发送响应 */
    public function send(): void
    {
        http_response_code($this->httpCode);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($this->headers as $k => $v) {
            header($k . ': ' . $v, true);
        }
        echo json_encode([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE);
    }

    /** 将对象转换为字符串时自动发送 */
    public function __toString(): string
    {
        // 注意：__toString 不能发送 header，这里仅返回字符串
        return json_encode([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE);
    }
}
