<?php

namespace Modules\Article\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class BaseRequest extends FormRequest
{
    /**
     * 判断用户是否有请求权限
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * 获取自定义验证规则的错误消息
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * 输出json数据
     * @param string $message
     * @param int $code
     * @param array $data
     */
    public function resultErrorAjax(string $message, int $code = -1, array $data = [])
    {
        $code = $code == -1 ? config('articlecommon.error') : $code;
        $jsonArr = [
            "state" => "error",
            "code" => $code,
            "message" => $message,
            "data" => $data,
        ];
        echo json_encode($jsonArr, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
