<?php

namespace Modules\Article\Http\Requests\Admin;

use Modules\Article\Http\Requests\BaseRequest;

class ArticleCatEditRequest extends BaseRequest
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
     * 获取规则
     * @return string[]
     */
    public function newRules()
    {
        return [
            'id' => 'nullable|integer|min:1',
            'cat_pid' => 'nullable|integer|min:1',
            'name' => 'required|string|min:1|max:100',
            'sub_name' => 'nullable|string|min:1|max:100',
            'cat_pic' => 'nullable|string|min:1|max:255',
            'cat_describe' => 'nullable|string|min:1',
            'cat_content' => 'nullable|string|min:1',
            'cat_seo' => 'nullable|string|min:1|max:255',
            'cat_keys' => 'nullable|string|min:1',
            'cat_outer_chain' => 'nullable|url',
            'is_recommend' => 'required|integer|min:0|max:1',
            'status' => 'required|integer|min:0|max:1',
            'sort' => 'required|integer|min:0|max:100',
        ];
    }

    /**
     * 获取自定义验证规则的错误消息
     * @return array
     */
    public function messages()
    {
        return [
//            'phone.regex' => "请输入正确的 :attribute",
        ];
    }

    /**
     * 获取自定义参数别名
     * @return string[]
     */
    public function attributes()
    {
        return [
            "cat_pid" => "上级分类",
            "name" => "分类名称",
            "sub_name" => "分类副名称",
            "cat_pic" => "缩略图",
            "cat_describe" => "描述",
            "cat_content" => "内容",
            "cat_seo" => "SEO",
            "cat_keys" => "关键词",
            "cat_outer_chain" => "外链地址",
            "is_recommend" => "推荐",
            "status" => "状态",
            "sort" => "排序",
        ];
    }

    /**
     * 验证规则
     */
    public function check()
    {
        $validator = \Validator::make($this->all(), $this->newRules(), $this->messages(), $this->attributes());
        $error = $validator->errors()->first();
        if($error){
            return $this->resultErrorAjax($error);
        }
    }
}
