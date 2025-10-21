-- 数据库表结构（MySQL 5.7+ / MariaDB 10.2+）
SET NAMES utf8mb4;
SET time_zone = '+08:00';

-- 管理员表
CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL COMMENT '账号',
  `password` VARCHAR(64) NOT NULL COMMENT '密码',
  `salt` VARCHAR(32) NOT NULL COMMENT '密码盐值',
  `name` VARCHAR(50) DEFAULT NULL COMMENT '姓名',
  `email` VARCHAR(100) DEFAULT NULL COMMENT '邮箱',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- API 分类表
CREATE TABLE IF NOT EXISTS `api_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT '分类名称',
  `parent_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父分类ID',
  `sort` INT NOT NULL DEFAULT 0 COMMENT '排序',
  `api_count` INT NOT NULL DEFAULT 0 COMMENT 'API数量',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `deleted_at` DATETIME DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 分类表';

-- API 表
CREATE TABLE IF NOT EXISTS `apis` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL COMMENT 'API名称',
  `category_id` INT UNSIGNED NOT NULL COMMENT '分类ID',
  `request_url` VARCHAR(500) NOT NULL COMMENT '请求地址',
  `request_method` VARCHAR(20) NOT NULL COMMENT '请求方式',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态（1启用0禁用）',
  `format` VARCHAR(20) NOT NULL COMMENT '返回格式（JSON/XML）',
  `description` TEXT COMMENT '描述',
  `call_count` INT NOT NULL DEFAULT 0 COMMENT '调用量',
  `call_limit` VARCHAR(100) DEFAULT NULL COMMENT '调用限制',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` DATETIME DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 表';

-- API 参数表
CREATE TABLE IF NOT EXISTS `api_params` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_id` INT UNSIGNED NOT NULL COMMENT 'API的ID',
  `param_type` VARCHAR(20) NOT NULL COMMENT '参数类型（header/body）',
  `param_name` VARCHAR(100) NOT NULL COMMENT '参数名',
  `data_type` VARCHAR(50) NOT NULL COMMENT '数据类型（string/int/array等）',
  `is_required` TINYINT NOT NULL DEFAULT 0 COMMENT '是否必填',
  `default_value` VARCHAR(200) DEFAULT NULL COMMENT '默认值',
  `enum_values` TEXT COMMENT '枚举值（JSON格式）',
  `description` VARCHAR(500) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`id`),
  KEY `idx_api_id` (`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 参数表';

-- API 错误码表
CREATE TABLE IF NOT EXISTS `api_errors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_id` INT UNSIGNED NOT NULL COMMENT 'API的ID',
  `error_code` VARCHAR(50) NOT NULL COMMENT '错误码',
  `error_msg` VARCHAR(200) NOT NULL COMMENT '错误信息',
  `solution` TEXT COMMENT '解决方案',
  PRIMARY KEY (`id`),
  KEY `idx_api_id` (`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 错误码表';

-- API 历史版本表
CREATE TABLE IF NOT EXISTS `api_versions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_id` INT UNSIGNED NOT NULL COMMENT 'API的ID',
  `version` VARCHAR(20) NOT NULL COMMENT '版本号',
  `changes` TEXT COMMENT '变更内容',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_api_id` (`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 历史版本表';

-- API 代码示例表
CREATE TABLE IF NOT EXISTS `api_code_samples` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_id` INT UNSIGNED NOT NULL COMMENT 'API的ID',
  `language` VARCHAR(50) NOT NULL COMMENT '语言（php/java/python等）',
  `code` TEXT COMMENT '示例代码',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_api_id` (`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 代码示例表';

-- 公告表
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL COMMENT '标题',
  `content` TEXT COMMENT '内容',
  `read_count` INT NOT NULL DEFAULT 0 COMMENT '阅读量',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态（1启用0禁用）',
  `is_top` TINYINT NOT NULL DEFAULT 0 COMMENT '是否置顶',
  `expire_time` DATETIME DEFAULT NULL COMMENT '到期时间',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `deleted_at` DATETIME DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_is_top` (`is_top`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告表';

-- 友情链接表
CREATE TABLE IF NOT EXISTS `friend_links` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT '名称',
  `url` VARCHAR(500) NOT NULL COMMENT '地址',
  `category` VARCHAR(50) DEFAULT NULL COMMENT '分类',
  `sort` INT NOT NULL DEFAULT 0 COMMENT '排序',
  `is_show` TINYINT NOT NULL DEFAULT 1 COMMENT '是否显示',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_is_show` (`is_show`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='友情链接表';

-- 赞助商表
CREATE TABLE IF NOT EXISTS `sponsors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT '名称',
  `logo` VARCHAR(500) DEFAULT NULL COMMENT 'LOGO路径',
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT '金额',
  `start_date` DATE DEFAULT NULL COMMENT '开始日期',
  `end_date` DATE DEFAULT NULL COMMENT '结束日期',
  `status` TINYINT NOT NULL DEFAULT 1 COMMENT '状态',
  `official_url` VARCHAR(500) DEFAULT NULL COMMENT '官网地址',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='赞助商表';

-- API 调用日志表
CREATE TABLE IF NOT EXISTS `api_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_id` INT UNSIGNED NOT NULL COMMENT 'API的ID',
  `ip` VARCHAR(50) DEFAULT NULL COMMENT 'IP地址',
  `params` TEXT COMMENT '请求参数（脱敏）',
  `response_code` INT DEFAULT NULL COMMENT '响应状态码',
  `response_time` INT DEFAULT NULL COMMENT '响应时间（毫秒）',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '调用时间',
  PRIMARY KEY (`id`),
  KEY `idx_api_id_created_at` (`api_id`, `created_at`),
  KEY `idx_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API 调用日志表';

-- 系统操作日志表
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NOT NULL COMMENT '管理员ID',
  `module` VARCHAR(50) DEFAULT NULL COMMENT '模块',
  `action` VARCHAR(50) DEFAULT NULL COMMENT '操作',
  `content` TEXT COMMENT '内容',
  `result` TINYINT DEFAULT 1 COMMENT '结果（1成功0失败）',
  `ip` VARCHAR(50) DEFAULT NULL COMMENT 'IP地址',
  `device` VARCHAR(200) DEFAULT NULL COMMENT '设备信息',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `idx_admin_id_created_at` (`admin_id`, `created_at`),
  KEY `idx_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统操作日志表';

-- 系统配置表
CREATE TABLE IF NOT EXISTS `system_configs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `config_key` VARCHAR(100) NOT NULL COMMENT '配置键',
  `config_value` TEXT COMMENT '配置值',
  `config_desc` VARCHAR(200) DEFAULT NULL COMMENT '配置说明',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';
