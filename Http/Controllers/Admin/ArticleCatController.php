<?php
// @author liming
namespace Modules\Article\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Article\Http\Requests\Admin\ArticleCatEditRequest;
use Modules\Article\Entities\ArticleCat;
use Modules\Article\Http\Controllers\Controller;

class ArticleCatController extends Controller
{
    /**
     * 文章分类分页列表
     */
    public function list()
    {
        $statusArr = ArticleCat::getStatusArr();
        $statusTest = implode("|", $statusArr);
        return view('articleview::admin.article_cat.list', compact('statusTest'));
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $list = ArticleCat::orderBy("sort")->get();
        foreach ($list as &$item){
            $item["show_cat_pic"] = $item->show_cat_pic;
        }
        return $this->success($list);
    }

    /**
     * 新增|编辑文章分类信息
     * @param $id
     */
    public function edit(ArticleCatEditRequest $request)
    {
        if($request->isMethod('post')) {
            $request->check();
            $data = $request->post();
            if(isset($data["id"])){
                $info = ArticleCat::where("id",$data["id"])->first();
                if(!$info) return $this->failed('数据不存在');

                // 处理字段内容
                $cat_pid = $data["cat_pid"] ?? 0;
                if($cat_pid > 0){
                    $pInfo = ArticleCat::where("id",$cat_pid)->first();
                    if(!$pInfo) return $this->failed('上级分类不存在');

                    $cids = ArticleCat::getCids($info->id);
                    if(in_array($cat_pid, $cids)) return $this->failed('不能选择自己或者自己的下级作为自己的上级');
                }
                $info->cat_pid = $cat_pid;
            }else{
                $info = new ArticleCat();

                // 处理字段内容
                $info->cat_pid = $data["cat_pid"] ?? 0;
                if($info->cat_pid > 0){
                    $pInfo = ArticleCat::where("id",$info->cat_pid)->first();
                    if(!$pInfo) return $this->failed('上级分类不存在');
                }
            }

            $info->name = $data["name"];
            $info->sub_name = $data["sub_name"] ?? "";
            $info->ulevels = "";    // todo::会员等级预览功能 预留字段
            $info->cat_outer_chain = $data["cat_outer_chain"] ?? "";
            $info->cat_seo = $data["cat_seo"] ?? "";
            $info->cat_keys = $data["cat_keys"] ?? "";
            $info->cat_describe = $data["cat_describe"] ?? "";
            $info->cat_content = $data["cat_content"] ?? "";
            $info->sort = $data["sort"];

            $statusArr = $info->getStatusArr();
            if(!isset($statusArr[$data["status"]])) return $this->failed('状态值不存在');
            if(isset($data["id"]) && $data["status"] == 0){ // 关闭状态
                $cids = ArticleCat::getCids($info->id);
                $infos = ArticleCat::where([
                    ["status", "=", "1"],
                    ["id", "!=", $info->id],
                ])->whereIn("id", $cids)->get();
                if(count($infos) > 0) return $this->failed('当前分类有子分类是开启状态，不能关闭当前分类');
            }
            $info->status = $data["status"];


            $info->is_recommend = $data["is_recommend"];
            $isRecommendArr = $info->getRecommendArr();
            if(!isset($isRecommendArr[$info->is_recommend])) return $this->failed('推荐值不存在');

            $info->cat_pic = $data["cat_pic"] ?? "";
            if($info->cat_pic != "" && !file_exists($info->cat_pic)){
                return $this->failed('上传的缩略图不存在');
            }

            try {
                if(!$info->save()) return $this->failed("操作失败");
                return $this->success();
            }catch (\Exception $e){
                return $this->failed($e->getMessage());
            }
        } else {
            $id = $request->input('id') ?? 0;
            $catArr = ArticleCat::getCatArr();
            if($id > 0){
                $info = ArticleCat::where('id',$id)->first();
                if(!$info) return $this->failed('数据不存在');
                $info["show_cat_pic"] = $info->show_cat_pic;
                $title = "编辑文章分类";
            }else{
                $info = new ArticleCat();
                $title = "新增文章分类";
            }
            return view('articleview::admin.article_cat.edit', compact('info', 'title', 'catArr'));
        }
    }

    /**
     * 删除文章分类
     */
    public function del(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');

            $info = ArticleCat::where('id',$id)->first();
            if(!$info) return $this->failed("数据不存在");

            try {
                $catArr = ArticleCat::getCids($id);
                if(!empty($catArr)){
                    if(ArticleCat::whereIn("id", $catArr)->delete()) return $this->success();
                }
                return $this->failed('操作失败');
            }catch (\Exception $e){
                $msg = $e->getMessage();
                $count = substr_count($msg, "1451 Cannot delete");
                if($count > 0){
                    $msg = "已有关联的文章，不能删除文章分类";
                }
                return $this->failed($msg);
            }
        }
        return $this->failed('请求出错.');
    }

    /**
     * 状态：开启 关闭功能
     */
    public function saveStatus(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            $status = $request->input("status");
            if($status === "true"){
                $status = 1;
            }else if($status === "false"){
                $status = 0;
            }
            $statusArr = ArticleCat::getStatusArr();
            if(!isset($statusArr[$status])) return $this->failed('状态值不存在');

            $info = ArticleCat::where('id',$id)->first();
            if(!$info) return $this->failed("数据不存在");
            if($status == 0){ // 关闭状态
                $cids = ArticleCat::getCids($info->id);
                $infos = ArticleCat::where([
                    ["status", "=", "1"],
                    ["id", "!=", $info->id],
                ])->whereIn("id", $cids)->get();
                if(count($infos) > 0) return $this->failed('当前分类有子分类是开启状态，不能关闭当前分类');
            }
            $info->status = $status;

            try {
                if($info->save()) return $this->success();
                return $this->failed('操作失败');
            }catch (\Exception $e){
                return $this->failed($e->getMessage());
            }
        }
        return $this->failed('请求出错.');
    }

    /**
     * 推荐：开启 关闭功能
     */
    public function saveRecommend(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            $is_recommend = $request->input("is_recommend");
            if($is_recommend === "true"){
                $is_recommend = 1;
            }else if($is_recommend === "false"){
                $is_recommend = 0;
            }
            $isRecommendArr = ArticleCat::getRecommendArr();
            if(!isset($isRecommendArr[$is_recommend])) return $this->failed('推荐值不存在');
            $info = ArticleCat::where('id',$id)->first();
            if(!$info) return $this->failed("数据不存在");
            $info->is_recommend = $is_recommend;

            try {
                if($info->save()) return $this->success();
                return $this->failed('操作失败');
            }catch (\Exception $e){
                return $this->failed($e->getMessage());
            }
        }
        return $this->failed('请求出错.');
    }
}
