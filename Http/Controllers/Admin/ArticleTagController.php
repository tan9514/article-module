<?php
// @author liming
namespace Modules\Article\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Article\Http\Controllers\Controller;
use Modules\Article\Http\Requests\Admin\ArticleTagEditRequest;
use Modules\Article\Entities\ArticleTag;

class ArticleTagController extends Controller
{
    /**
     * 文章标签分页列表
     */
    public function list()
    {
        return view('articleview::admin.article_tag.list');
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];
        //获取总条数
        $count = ArticleTag::where($where)->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = ArticleTag::where($where)->offset($offset)->limit($pagesize)->orderBy("id", "desc")->get();

        return $this->success(compact('list', 'count'));
    }

    /**
     * 新增|编辑文章标签信息
     * @param $id
     */
    public function edit(ArticleTagEditRequest $request)
    {
        if($request->isMethod('post')) {
            $request->check();
            $data = $request->post();

            if(isset($data["id"])){
                $info = ArticleTag::where("id",$data["id"])->first();
                if(!$info) return $this->failed('数据不存在');
            }else{
                $info = new ArticleTag();
            }

            $info->name = $data["name"];
            try {
                if(!$info->save()) return $this->failed("操作失败");
                return $this->success();
            }catch (\Exception $e){
                return $this->failed($e->getMessage());
            }
        } else {
            $id = $request->input('id') ?? 0;
            if($id > 0){
                $info = ArticleTag::where('id',$id)->first();
                $title = "编辑文章标签";
            }else{
                $info=(object)[];
                $title = "新增文章标签";
            }
            return view('articleview::admin.article_tag.edit', compact('info', 'title'));
        }
    }

    /**
     * 删除文章标签
     */
    public function del(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            if(is_array($id)){
                // 数组删除
                try {
                    if(ArticleTag::whereIn("id", $id)->delete()) return $this->success();
                    return $this->failed('操作失败');
                }catch (\Exception $e){
                    $msg = $e->getMessage();
                    $count = substr_count($msg, "1451 Cannot delete");
                    if($count > 0){
                        $msg = "已有关联的文章，不能删除标签";
                    }
                    return $this->failed($msg);
                }
            }else{
                // 单条数据
                $info = ArticleTag::where('id',$id)->first();
                if(!$info) return $this->failed("数据不存在");
                try {
                    if($info->delete()) return $this->success();
                    return $this->failed('操作失败');
                }catch (\Exception $e){
                    $msg = $e->getMessage();
                    $count = substr_count($msg, "1451 Cannot delete");
                    if($count > 0){
                        $msg = "已有关联的文章，不能删除标签";
                    }
                    return $this->failed($msg);
                }
            }
        }
        return $this->failed('请求出错.');
    }

}
