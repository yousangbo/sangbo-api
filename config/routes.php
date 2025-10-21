<?php
use Core\Router;
use App\Controllers\Frontend\HomeController;
use App\Controllers\Admin\DashboardController;

// 前端路由
Router::get('/', [HomeController::class, 'index']);
Router::get('/ping', [HomeController::class, 'ping']);

// 示例 RESTful 路由（API资源）
// GET /apis -> index, POST /apis -> store
// GET /apis/{id} -> show, PUT/PATCH /apis/{id} -> update, DELETE /apis/{id} -> destroy
Router::restful('/apis', App\Controllers\Frontend\ApiController::class);

// 后台路由（以 /admin 前缀区分）
Router::get('/admin', [DashboardController::class, 'index']);
