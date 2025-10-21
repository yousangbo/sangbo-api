<?php
namespace Core;

/**
 * 工具类
 * - 密码加密（MD5+盐值）
 * - 文件上传处理
 * - 图片压缩
 * - 随机字符串生成
 * - 时间格式化
 * - 数组处理
 */
class Helper
{
    /** 密码加密：md5( md5(password) + salt ) */
    public static function encryptPassword(string $password, string $salt): string
    {
        return md5(md5($password) . $salt);
    }

    /** 生成随机字符串 */
    public static function randomString(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /** 时间格式化 */
    public static function formatDatetime(?int $timestamp = null, string $format = 'Y-m-d H:i:s'): string
    {
        $timestamp = $timestamp ?? time();
        return date($format, $timestamp);
    }

    /** 数组安全取值 */
    public static function arrayGet(array $array, string $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    /**
     * 处理上传文件（单文件）
     * @param array $file $_FILES['field']
     * @param string $destinationDir 目标目录
     * @param array $allowed 扩展名白名单
     * @return array{success:bool,path?:string,error?:string}
     */
    public static function handleUpload(array $file, string $destinationDir, array $allowed = ['jpg','jpeg','png','gif','pdf']): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => '上传失败，错误码：' . ($file['error'] ?? -1)];
        }
        if (!is_dir($destinationDir)) @mkdir($destinationDir, 0777, true);
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            return ['success' => false, 'error' => '不支持的文件类型'];
        }
        $basename = uniqid('upload_', true) . '.' . $ext;
        $dest = rtrim($destinationDir, '/') . '/' . $basename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => '文件移动失败'];
        }
        return ['success' => true, 'path' => $dest];
    }

    /**
     * 图片压缩（依赖 GD）
     */
    public static function compressImage(string $src, string $dest, int $quality = 80): bool
    {
        if (!extension_loaded('gd')) return false;
        if (!file_exists($src)) return false;
        $info = getimagesize($src);
        if ($info === false) return false;
        [$width, $height, $type] = $info;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($src);
                $result = imagejpeg($image, $dest, $quality);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($src);
                // PNG 的质量参数 0-9，反向映射
                $pngQuality = (int)round((100 - $quality) / 10);
                $result = imagepng($image, $dest, $pngQuality);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($src);
                $result = imagegif($image, $dest);
                break;
            default:
                return false;
        }
        if (isset($image) && is_resource($image)) {
            imagedestroy($image);
        }
        return (bool)$result;
    }
}
