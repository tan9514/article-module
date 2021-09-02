<?php
// @author liming
namespace Modules\Article\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Article\Http\Controllers\Controller;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleAgree;
use Modules\Article\Entities\ArticleAttrs;
use Modules\Article\Entities\ArticleCat;
use Modules\Article\Entities\ArticleCats;
use Modules\Article\Entities\ArticleFavorite;
use Modules\Article\Entities\ArticleRead;
use Modules\Article\Entities\ArticleTags;

class ArticleRecycleBinController extends Controller
{
    /**
     * 回收站分页列表
     */
    public function list()
    {
        $catArr = ArticleCat::getCatArr();
        return view('articleview::admin.article_recycle_bin.list', compact('catArr'));
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];

        $title = $request->input('title');
        if($title != "") $where[] = ["article.title", "like", "%{$title}%"];

        $cat_id = $request->input("cat_id");
        if($cat_id != "") $where[] = ["acs.cat_id", "=", $cat_id];

        //获取总条数
        $count = Article::onlyTrashed()
            ->leftJoin('article_cats as acs','article.id','=','acs.article_id')
            ->leftJoin('article_attrs as aas','article.id','=','aas.article_id')
            ->leftJoin('article_tags as ats','article.id','=','ats.article_id')
            ->where($where)
            ->distinct("article.id")
            ->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = Article::onlyTrashed()
            ->leftJoin('article_cats as acs','article.id','=','acs.article_id')
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
     * 回收站文章查看功能
     * @param $id
     */
    public function details(Request $request)
    {
        $id = $request->input('id') ?? 0;
        $info = Article::onlyTrashed()->where('id',$id)->first();
        $title = "查看文章";
        if(!$info) return $this->failed("Data does not exist.");
        $info["show_pic"] = $info->show_pic;
        $info['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
            ->where("article_cats.article_id", $info->id)->pluck("name");
        $info['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
            ->where("article_attrs.article_id", $info->id)->pluck("name");
        $info['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
            ->where("article_tags.article_id", $info->id)->pluck("name");

        return view('articleview::admin.article_recycle_bin.edit', compact('info', 'title'));
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
                DB::beginTransaction();
                try {
                    ArticleCats::whereIn("article_id", $id)->delete();
                    ArticleAttrs::whereIn("article_id", $id)->delete();
                    ArticleTags::whereIn("article_id", $id)->delete();
                    ArticleRead::whereIn("article_id", $id)->delete();
                    ArticleAgree::whereIn("article_id", $id)->delete();
                    ArticleFavorite::whereIn("article_id", $id)->delete();

                    if(Article::whereIn("id", $id)->forceDelete()){
                        DB::commit();
                        return $this->success();
                    }else{
                        throw new \Exception("操作失败");
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->failed($e->getMessage());
                }
            } else {
                $info = Article::onlyTrashed()->where('id', $id)->first();
                if (!$info) return $this->failed("数据不存在");

                DB::beginTransaction();
                try {
                    ArticleCats::where("article_id", $info->id)->delete();
                    ArticleAttrs::where("article_id", $info->id)->delete();
                    ArticleTags::where("article_id", $info->id)->delete();
                    ArticleRead::whereIn("article_id", $info->id)->delete();
                    ArticleAgree::whereIn("article_id", $info->id)->delete();
                    ArticleFavorite::whereIn("article_id", $info->id)->delete();

                    if (!$info->forceDelete()) throw new \Exception("操作失败");
                    DB::commit();
                    return $this->success();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->failed($e->getMessage());
                }
            }
        }
        return $this->failed('请求出错.');
    }

    /**
     * 文章移出回收站
     */
    public function recovery(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            if(is_array($id)) {
                // 数组删除
                try {
                    if(Article::whereIn("id", $id)->restore()){
                        return $this->success();
                    }else{
                        throw new \Exception("操作失败");
                    }
                } catch (\Exception $e) {
                    return $this->failed($e->getMessage());
                }
            } else {
                $info = Article::onlyTrashed()->where('id', $id)->first();
                if (!$info) return $this->failed("数据不存在");

                try {
                    if (!$info->restore()) throw new \Exception("操作失败");
                    return $this->success();
                } catch (\Exception $e) {
                    return $this->failed($e->getMessage());
                }
            }
        }
        return $this->failed('请求出错.');
    }

}
