<?php

namespace Modules\Article\Http\Requests\Admin;

use Modules\Article\Http\Requests\BaseRequest;

class ArticleEditRequest extends BaseRequest
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
            'title' => 'required|string|min:1|max:255',
            'sub_title' => 'nullable|string|min:1|max:255',
            'pic' => 'required|string|min:1|max:255',
            "source" => "nullable|string|min:1|max:255",
            'author' => 'required|string|min:1|max:255',
            'outer_chain' => 'nullable|url',
            'keys' => 'nullable|string|min:1',
            'describe' => 'nullable|string|min:1',
            'virtual_read' => 'required|integer|min:0',
            'virtual_agree' => 'required|integer|min:0',
            'virtual_favorite' => 'required|integer|min:0',
            'status' => 'required|integer|min:0|max:1',
            'sort' => 'required|integer|min:0|max:100',
            'editorValue' => 'required|string|min:1',
            "cat_ids" => "nullable|string",
            "attrs" => "nullable|string|min:1",
            "tags" => "nullable|string|min:1",
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
            "title" => "主标题",
            "sub_title" => "副标题",
            "pic" => "缩略图",
            "source" => "来源",
            "author" => "作者",
            "outer_chain" => "外链地址",
            "keys" => "关键词",
            "describe" => "摘要",
            "virtual_read" => "虚拟浏览次数",
            "virtual_agree" => "虚拟点赞量",
            "virtual_favorite" => "虚拟收藏量",
            "status" => "状态",
            "sort" => "排序",
            "editorValue" => "文章内容",
            "cat_ids" => "分类",
            "attrs" => "属性",
            "tags" => "标签",
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
