<?php

namespace Modules\Article\Http\Requests\Api;

use Modules\Article\Http\Requests\BaseRequest;

class ArticlePageListRequest extends BaseRequest
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
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1',
            'cat_id' => 'nullable|integer|min:1',
            'title' => 'nullable|string',
            'keys' => 'nullable|string',
            'author' => 'nullable|string',
            'attr' => 'nullable|string',
            'tag' => 'nullable|string',
            'sort' => 'nullable|integer|min:1|max:3',
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
            "page" => "页数",
            "limit" => "条数",
            "cat_id" => "分类ID",
            "title" => "文章标题",
            "keys" => "关键词",
            "author" => "作者",
            'attr' => '属性',
            'tag' => '标签',
            'sort' => '排序方式',
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
