<?php
namespace Core;

use PDO;
use PDOException;

/**
 * 数据库连接类（基于 PDO）
 * - 支持预处理语句
 * - 支持事务
 * - 错误处理
 *
 * 使用示例：
 * $db = Database::getInstance();
 * $rows = $db->fetchAll('SELECT * FROM admin WHERE id > :id', ['id' => 1]);
 */
class Database
{
    /** @var array 配置 */
    protected array $config;

    /** @var PDO|null PDO 实例 */
    protected ?PDO $pdo = null;

    /** @var array<string,self> 多连接实例 */
    protected static array $instances = [];

    /**
     * 构造函数私有化，防止外部直接实例化
     */
    private function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * 获取数据库实例（支持多连接）
     *
     * @param string $name 连接名，对应配置中的键名
     */
    public static function getInstance(string $name = 'default'): self
    {
        if (!isset(self::$instances[$name])) {
            // 加载数据库配置
            $configFile = __DIR__ . '/../config/database.php';
            if (!file_exists($configFile)) {
                // 若用户未提供正式配置，则回退到 example
                $configFile = __DIR__ . '/../config/database.php.example';
            }
            /** @var array $configs */
            $configs = require $configFile;
            if (!isset($configs[$name])) {
                throw new \RuntimeException("数据库配置 '{$name}' 不存在");
            }
            self::$instances[$name] = new self($configs[$name]);
        }
        return self::$instances[$name];
    }

    /**
     * 建立 PDO 连接
     */
    protected function connect(): void
    {
        if ($this->pdo instanceof PDO) {
            return;
        }
        $cfg = $this->config;
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['driver'] ?? 'mysql',
            $cfg['host'] ?? '127.0.0.1',
            (int)($cfg['port'] ?? 3306),
            $cfg['database'] ?? '',
            $cfg['charset'] ?? 'utf8mb4'
        );
        $options = $cfg['options'] ?? [];
        try {
            $this->pdo = new PDO(
                $dsn,
                $cfg['username'] ?? 'root',
                $cfg['password'] ?? '',
                $options
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('数据库连接失败：' . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * 获取原始 PDO 实例
     */
    public function getPdo(): PDO
    {
        if (!$this->pdo) {
            $this->connect();
        }
        return $this->pdo;
    }

    /**
     * 执行 SQL（预处理）
     *
     * @param string $sql SQL 语句，可包含命名参数
     * @param array $params 绑定参数
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->getPdo()->prepare($sql);
            foreach ($params as $key => $value) {
                $k = (string)$key;
                $param = is_int($key) ? $key + 1 : ((substr($k, 0, 1) === ':') ? $k : ':' . $k);
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new \RuntimeException('SQL 执行失败：' . $e->getMessage() . ' | SQL: ' . $sql, (int)$e->getCode(), $e);
        }
    }

    /** 按行数组返回全部结果 */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /** 返回单行 */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** 返回首行首列（标量） */
    public function fetchColumn(string $sql, array $params = [], int $column = 0)
    {
        return $this->query($sql, $params)->fetchColumn($column);
    }

    /** 执行非查询语句（INSERT/UPDATE/DELETE）返回影响行数 */
    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /** 事务开始 */
    public function beginTransaction(): void
    {
        $this->getPdo()->beginTransaction();
    }

    /** 提交事务 */
    public function commit(): void
    {
        $this->getPdo()->commit();
    }

    /** 回滚事务 */
    public function rollBack(): void
    {
        $this->getPdo()->rollBack();
    }

    /** 获取最后插入的自增 ID */
    public function lastInsertId(): string
    {
        return $this->getPdo()->lastInsertId();
    }
}
