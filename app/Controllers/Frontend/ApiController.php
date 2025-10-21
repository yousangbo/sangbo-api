<?php
namespace App\Controllers\Frontend;

use Core\Request;
use Core\Response;
use Core\Validator;

/**
 * API 资源控制器（示例）
 */
class ApiController
{
    public function index(Request $request, array $params)
    {
        // 示例：返回空列表
        return Response::json(['list' => [], 'total' => 0]);
    }

    public function show(Request $request, array $params)
    {
        $id = $params['id'] ?? null;
        return Response::json(['id' => $id]);
    }

    public function store(Request $request, array $params)
    {
        $data = $request->input();
        $errors = Validator::validate($data, [
            'name' => 'required|type:string|max:200',
            'request_url' => 'required|type:string|max:500',
        ]);
        if ($errors) {
            return Response::json(['errors' => $errors], 422, '参数验证失败', 422);
        }
        return Response::json(['id' => 1, 'name' => $data['name'] ?? '']);
    }

    public function update(Request $request, array $params)
    {
        $id = (int)($params['id'] ?? 0);
        $data = $request->input();
        return Response::json(['updated' => true, 'id' => $id, 'data' => $data]);
    }

    public function destroy(Request $request, array $params)
    {
        $id = (int)($params['id'] ?? 0);
        return Response::json(['deleted' => true, 'id' => $id]);
    }
}
