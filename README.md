# Sangbo API 管理系统（基础架构）

一个采用轻量 MVC 架构的 API 管理系统脚手架，用于统一管理 API 文档、参数、错误码、调用日志等信息。本仓库目前处于第一阶段：项目基础架构搭建 + 数据库设计。

## 技术栈
- PHP >= 7.4（遵循 PSR-4 自动加载）
- MySQL 5.7+ 或 MariaDB 10.2+
- Web Server: Apache（提供 .htaccess 重写规则）或 Nginx（自行配置重写）

## 目录结构（MVC）
```
sangbo-api/
├── public/                 # Web根目录
│   ├── index.php          # 前端入口
│   ├── admin.php          # 后台入口
│   ├── install/           # 安装程序目录
│   ├── static/            # 静态资源
│   │   ├── css/          # 样式文件
│   │   ├── js/           # JavaScript文件
│   │   └── images/       # 图片资源
│   └── .htaccess         # Apache重写规则
├── app/                   # 应用核心
│   ├── controllers/       # 控制器目录
│   │   ├── admin/        # 后台控制器
│   │   └── frontend/     # 前端控制器
│   ├── models/           # 模型目录
│   ├── views/            # 视图目录
│   │   ├── admin/        # 后台视图
│   │   └── frontend/     # 前端视图
│   └── middleware/       # 中间件目录
├── config/               # 配置文件
│   ├── database.php.example
│   ├── app.php
│   └── routes.php
├── core/                 # 核心类库
├── database/             # 数据库文件
│   ├── schema.sql       # 数据库表结构
│   └── seed.sql         # 初始数据
├── storage/             # 存储目录
│   ├── logs/           # 日志文件
│   ├── cache/          # 缓存文件
│   ├── uploads/        # 上传文件
│   └── backups/        # 备份文件
├── vendor/              # Composer依赖
├── composer.json        # Composer配置
├── .gitignore          # Git忽略文件
└── README.md           # 项目说明文档
```

## 核心类库
- core/Database.php：PDO 封装（预处理、事务、错误处理）
- core/Router.php：路由（RESTful、路由参数、中间件、404）
- core/Request.php：请求（参数、上传、请求头、IP）
- core/Response.php：响应（JSON、HTTP 状态码、统一格式）
- core/Validator.php：参数验证与安全过滤（必填、类型、长度、XSS/SQL 过滤、邮箱、URL）
- core/Cache.php：文件缓存（读写删、TTL，预留 Redis 接口）
- core/Logger.php：日志（按日期、级别、支持异步缓冲写入）
- core/Helper.php：工具（密码 MD5+盐、上传、图片压缩、随机串、时间格式化、数组取值）
- core/Middleware.php：中间件基类

应用中提供示例中间件：
- app/middleware/AuthMiddleware.php：权限验证（示例）
- app/middleware/LogMiddleware.php：请求日志
- app/middleware/CsrfMiddleware.php：CSRF 校验（示例）

## 安装要求
- PHP >= 7.4，开启 PDO 扩展（pdo_mysql）
- MySQL 5.7+ 或 MariaDB 10.2+
- Apache/Nginx 任一可用，需支持 URL 重写

## 快速开始
1. 克隆项目后，安装 Composer 依赖（可选）
   ```bash
   composer install
   ```
2. 复制数据库配置模板并填写真实信息
   ```bash
   cp config/database.php.example config/database.php
   ```
3. 创建数据库并导入结构与初始数据
   ```sql
   -- 创建数据库（示例）
   CREATE DATABASE sangbo_api DEFAULT CHARACTER SET utf8mb4;
   USE sangbo_api;
   SOURCE database/schema.sql;
   SOURCE database/seed.sql;
   ```
4. 配置 Web 根目录指向 public/ 目录，Apache 可使用自带 .htaccess。
5. 访问：
   - 前台入口：http://your-host/
   - 后台入口：http://your-host/admin

## 路由示例
- GET /          -> HomeController@index（JSON 输出）
- GET /ping      -> 返回 {"pong": true}
- RESTful /apis  -> ApiController（index/show/store/update/destroy）

## 其他说明
- 所有代码均使用中文注释，命名规范遵循 PSR：
  - 类名：大驼峰（PascalCase）
  - 方法名：小驼峰（camelCase）
  - 数据库字段：下划线命名
- .gitignore 已忽略 vendor/ 与存储目录内文件（保留目录结构）

## 许可证
当前为内部项目脚手架，后续可根据需要补充 License。
