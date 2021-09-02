<?php
// @author liming
namespace Modules\Article\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Article\Http\Controllers\Controller;
use Modules\Article\Http\Requests\Admin\ArticleEditFiledRequest;
use Modules\Article\Http\Requests\Admin\ArticleEditRequest;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleAttr;
use Modules\Article\Entities\ArticleAttrs;
use Modules\Article\Entities\ArticleCat;
use Modules\Article\Entities\ArticleCats;
use Modules\Article\Entities\ArticleTag;
use Modules\Article\Entities\ArticleTags;

class ArticleController extends Controller
{
    /**
     * 文章分页列表
     */
    public function list()
    {
        $statusArr = ArticleCat::getStatusArr();
        $statusTest = implode("|", $statusArr);
        $catArr = ArticleCat::getCatArr();
        $attrArr = ArticleAttr::all();
        $tagArr = ArticleTag::all();
        return view('articleview::admin.article.list', compact('statusArr', 'statusTest','catArr',  'attrArr', 'tagArr'));
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];

        $status = $request->input('status');
        if($status != "") $where[] = ["article.status", "=", $status];

        $title = $request->input('title');
        if($title != "") $where[] = ["article.title", "like", "%{$title}%"];

        $cat_id = $request->input("cat_id");
        if($cat_id != "") $where[] = ["acs.cat_id", "=", $cat_id];

        $attr_id = $request->input("attr_id");
        if($attr_id != "") $where[] = ["aas.attr_id", "=", $attr_id];

        $tag_id = $request->input("tag_id");
        if($tag_id != "") $where[] = ["ats.tag_id", "=", $tag_id];

        $source = $request->input("source");
        if($source != "") $where[] = ["article.source", "like", "%{$source}%"];

        //获取总条数
        $count = Article::leftJoin('article_cats as acs','article.id','=','acs.article_id')
            ->leftJoin('article_attrs as aas','article.id','=','aas.article_id')
            ->leftJoin('article_tags as ats','article.id','=','ats.article_id')
            ->where($where)
            ->distinct("article.id")
            ->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = Article::leftJoin('article_cats as acs','article.id','=','acs.article_id')
            ->leftJoin('article_attrs as aas','article.id','=','aas.article_id')
            ->leftJoin('article_tags as ats','article.id','=','ats.article_id')
            ->where($where)
            ->offset($offset)
            ->limit($pagesize)
            ->orderBy("article.sort")->orderBy("article.id", "desc")
            ->distinct("article.id")
            ->select("article.*")
            ->get();
        foreach ($list as &$item){
            $item["show_pic"] = $item->show_pic;
            $item['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
                ->where("article_cats.article_id", $item->id)->pluck("name");
            $item['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
                ->where("article_attrs.article_id", $item->id)->pluck("name");
            $item['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
                ->where("article_tags.article_id", $item->id)->pluck("name");
        }
        return $this->success(compact('list', 'count'));
    }

    /**
     * 新增|编辑文章信息
     * @param $id
     */
    public function edit(ArticleEditRequest $request)
    {
        if($request->isMethod('post')) {
            $request->check();
            $data = $request->post();

            if(isset($data["id"])){
                $info = Article::where("id",$data["id"])->first();
                if(!$info) return $this->failed('数据不存在');
            }else{
                $info = new Article();
            }

            $info->title = $data["title"];
            $info->sub_title = $data["sub_title"] ?? "";
            $info->source = $data["source"] ?? "";
            $info->author = $data["author"];
            $info->describe = $data["describe"] ?? "";
            $info->content = $data["editorValue"];
            $info->keys = $data["keys"] ?? "";
            $info->outer_chain = $data["outer_chain"] ?? "";
            $info->virtual_read = $data["virtual_read"];
            $info->virtual_agree = $data["virtual_agree"];
            $info->virtual_favorite = $data["virtual_favorite"];
            $info->sort = $data["sort"];

            $info->status = $data["status"];
            $statusArr = $info->getStatusArr();
            if(!isset($statusArr[$info->status])) return $this->failed('状态值不存在');

            $info->pic = $data["pic"];
            if(!file_exists($info->pic)) return $this->failed('上传的缩略图不存在');

            DB::beginTransaction();
            try {
                if(!$info->save()) throw new \Exception("操作失败");

                // 关联分类
                ArticleCats::where("article_id", $info->id)->delete();
                if($data["cat_ids"] != ""){
                    $catInfos = ArticleCat::whereIn("id", explode(",", $data["cat_ids"]))->get();
                    foreach ($catInfos as $catInfo){
                        $ArticleCatsModel = new ArticleCats();
                        $ArticleCatsModel->article_id = $info->id;
                        $ArticleCatsModel->cat_id = $catInfo->id;
                        if(!$ArticleCatsModel->save()) throw new \Exception("操作失败: 新增关联分类信息失败");
                    }
                }

                // 关联属性
                ArticleAttrs::where("article_id", $info->id)->delete();
                if($data["attrs"] != ""){
                    $attrArr = explode(",", $data["attrs"]);
                    foreach ($attrArr as $attrItem){
                        $attrInfo = ArticleAttr::where("name", $attrItem)->first();
                        if(!$attrInfo){
                            $attrInfo = new ArticleAttr();
                            $attrInfo->name = $attrItem;
                            if(!$attrInfo->save()) throw new \Exception("操作失败: 新增关联属性信息失败");
                        }
                        $ArticleAttrsModel = new ArticleAttrs();
                        $ArticleAttrsModel->article_id = $info->id;
                        $ArticleAttrsModel->attr_id = $attrInfo->id;
                        if(!$ArticleAttrsModel->save()) throw new \Exception("操作失败: 新增关联属性信息失败");
                    }
                }

                // 关联标签
                ArticleTags::where("article_id", $info->id)->delete();
                if($data["tags"] != ""){
                    $tagArr = explode(",", $data["tags"]);
                    foreach ($tagArr as $tagItem){
                        $tagInfo = ArticleTag::where("name", $tagItem)->first();
                        if(!$tagInfo){
                            $tagInfo = new ArticleTag();
                            $tagInfo->name = $tagItem;
                            if(!$tagInfo->save()) throw new \Exception("操作失败: 新增关联标签信息失败");
                        }
                        $ArticleTagsModel = new ArticleTags();
                        $ArticleTagsModel->article_id = $info->id;
                        $ArticleTagsModel->tag_id = $tagInfo->id;
                        if(!$ArticleTagsModel->save()) throw new \Exception("操作失败: 新增关联标签信息失败");
                    }
                }

                DB::commit();
                return $this->success();
            }catch (\Exception $e){
                DB::rollBack();
                return $this->failed($e->getMessage());
            }
        } else {
            $id = $request->input('id') ?? 0;
            if($id > 0){
                $info = Article::where('id',$id)->first();
                $title = "编辑文章";
                $info["show_pic"] = $info->show_pic;
                $info["attrs"] = ArticleAttrs::where("article_attrs.article_id", $info->id)
                    ->join('article_attr as aa','article_attrs.attr_id','=','aa.id')
                    ->pluck("aa.name")
                    ->toArray();
                if(count($info["attrs"]) > 0){
                    $info["attrs"] = implode(",", $info["attrs"]);
                }else{
                    $info["attrs"] = "";
                }
                $info["tags"] = ArticleTags::where("article_tags.article_id", $info->id)
                    ->join('article_tag as at','article_tags.tag_id','=','at.id')
                    ->pluck("at.name")
                    ->toArray();
                if(count($info["tags"]) > 0){
                    $info["tags"] = implode(",", $info["tags"]);
                }else{
                    $info["tags"] = "";
                }
            }else{
                $info = new Article();
                $title = "新增文章";
            }
            return view('articleview::admin.article.edit', compact('info', 'title'));
        }
    }

    /**
     * 批量修改文章分类
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function batchEdit(Request $request)
    {
        if($request->isMethod('post')) {
            $ids = $request->input("ids") ?? "";
            $catIds = $request->input("cat_ids") ?? "";

            if($ids == "") return $this->failed('数据不存在');
            $idsArr = explode(",", $ids);

            // 获取分类
            if($catIds != ""){
                $catIds = ArticleCat::whereIn("id", explode(",", $catIds))->get()->toArray();
            }else{
                $catIds = [];
            }

            // 修改数据
            DB::beginTransaction();
            try {
                foreach ($idsArr as $articleId){
                    $articleInfo = Article::where([
                        ["id", "=", $articleId],
                    ])->first();
                    if(empty($articleInfo)) throw new \Exception('勾选的文章数据不存在');

                    ArticleCats::where("article_id", $articleId)->delete();
                    foreach ($catIds as $articleCatInfo){
                        $ArticleCatsModel = new ArticleCats();
                        $ArticleCatsModel->article_id = $articleId;
                        $ArticleCatsModel->cat_id = $articleCatInfo["id"];
                        if(!$ArticleCatsModel->save()) throw new \Exception("操作失败: 新增关联分类信息失败");
                    }
                }

                DB::commit();
                return $this->success();
            }catch (\Exception $e){
                DB::rollBack();
                return $this->failed($e->getMessage());
            }
        } else {
            $id = $request->input('ids') ?? "";
            if($id == "") exit('
                    <div style="position: absolute; left: 50%;top: 50%;transform: translate(-50%, -50%);text-align: center;">
                        <img style="max-height: 265px;vertical-align: middle;" src="/layuimini/images/ic_404.png">
                        <div style="display: inline-block;text-align: center;vertical-align: middle;padding-left: 30px;">
                            <h1 style="color: #434e59;font-size: 72px;font-weight: 600;margin-bottom: 10px;"> </h1>
                            <div style="color: #777;font-size: 20px;line-height: 28px;margin-bottom: 16px;">请勾选要修改的数据！</div>
                        </div>
                    </div>
                ');

            $idArr = explode(",", $id);
            $infos = Article::whereIn("id", $idArr)->pluck("title")->toArray();
            if(count($infos) <= 0) exit('
                    <div style="position: absolute; left: 50%;top: 50%;transform: translate(-50%, -50%);text-align: center;">
                        <img style="max-height: 265px;vertical-align: middle;" src="/layuimini/images/ic_404.png">
                        <div style="display: inline-block;text-align: center;vertical-align: middle;padding-left: 30px;">
                            <h1 style="color: #434e59;font-size: 72px;font-weight: 600;margin-bottom: 10px;"> </h1>
                            <div style="color: #777;font-size: 20px;line-height: 28px;margin-bottom: 16px;">请勾选要修改的数据！</div>
                        </div>
                    </div>
                ');

            $title = "批量修改文章数据";
            return view('articleview::admin.article.batch_edit', compact('title', 'id', 'infos'));
        }
    }

    /**
     * ajax 获取所有分类  获取所有属性  获取所有标签  获取所有来源
     * @param Request $request
     * @return mixed
     * @author tan bing
     * @date 2021-06-17 16:56
     */
    public function xmSelect(Request $request)
    {
        function getArticleCat($pid,array $catIdArr = []){
            $list = ArticleCat::where("cat_pid", $pid)->select('id as value','name')->get()->toArray();
            foreach ($list as &$item){
                if(in_array($item["value"], $catIdArr)) $item['selected'] = true;
                $item["children"] = getArticleCat($item["value"], $catIdArr);
            }
            return $list;
        }

        $id = $request->input("id");
        if((int)$id > 0){
            // 重构分类
            $catIdArr = ArticleCats::where("article_id", $id)->pluck("cat_id")->toArray();
            $articleCat = getArticleCat(0, $catIdArr);
        }else{
            // 获取分类
            $articleCat = getArticleCat(0);
        }

        $list = compact("articleCat","id");
        return $this->success($list);
    }

    /**
     * 删除文章
     */
    public function del(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            if(is_array($id)) {
                // 数组删除
                try {
                    if(Article::whereIn("id", $id)->delete()) return $this->success();
                    throw new \Exception("操作失败");
                } catch (\Exception $e) {
                    return $this->failed($e->getMessage());
                }
            } else {
                $info = Article::where('id', $id)->first();
                if (!$info) return $this->failed("数据不存在");

                try {
                    if (!$info->delete()) throw new \Exception("操作失败");
                    return $this->success();
                } catch (\Exception $e) {
                    return $this->failed($e->getMessage());
                }
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
            $statusArr = Article::getStatusArr();
            if(!isset($statusArr[$status])) return $this->failed('状态值不存在');

            $info = Article::where('id',$id)->first();
            if(!$info) return $this->failed("数据不存在");
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
     * 文章单字段修改
     */
    public function saveField(ArticleEditFiledRequest $request)
    {
        if($request->isMethod('post')){
            $request->check();
            $id = $request->input('id');
            $info = Article::where('id',$id)->first();
            if(!$info) return $this->failed("数据不存在");

            $value = $request->input("value");
            $field = $request->input("field");
            switch ($field){
                case "sort": // 排序
                    if($value < 0 || $value > 100) return $this->failed('排序值只能为大于等于0 ~ 小于等于100');
                    $info->sort = $value;
                    break;
                default:
                    return $this->failed('支持的单字段不包含当前字段');
            }

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
