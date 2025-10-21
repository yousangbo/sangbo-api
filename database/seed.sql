-- 初始数据
SET NAMES utf8mb4;
SET time_zone = '+08:00';

-- 管理员
INSERT INTO `admin` (`username`, `password`, `salt`, `name`, `email`, `created_at`, `updated_at`) VALUES
('admin', '408cb9ae5614d855c2c7298a5e659be4', 'abc123def456gh78', '超级管理员', 'admin@example.com', NOW(), NOW());

-- API 分类
INSERT INTO `api_categories` (`name`, `parent_id`, `sort`, `api_count`, `created_at`) VALUES
('默认分类', 0, 0, 0, NOW()),
('用户服务', 0, 10, 0, NOW());

-- 示例 API
INSERT INTO `apis` (`name`, `category_id`, `request_url`, `request_method`, `status`, `format`, `description`, `call_count`, `call_limit`, `created_at`, `updated_at`) VALUES
('示例-获取用户信息', 2, '/apis/{id}', 'GET', 1, 'JSON', '根据ID获取用户信息', 0, NULL, NOW(), NOW());

-- 公告
INSERT INTO `announcements` (`title`, `content`, `read_count`, `status`, `is_top`, `created_at`) VALUES
('欢迎使用 Sangbo API 管理系统', '请先导入数据库并完成基本配置。', 0, 1, 1, NOW());

-- 系统配置
INSERT INTO `system_configs` (`config_key`, `config_value`, `config_desc`, `updated_at`) VALUES
('site_name', 'Sangbo API 管理系统', '站点名称', NOW()),
('timezone', 'Asia/Shanghai', '默认时区', NOW());
