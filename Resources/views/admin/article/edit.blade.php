@extends('admin.public.header')
@section('title',$title)
@section('listcontent')
    <form class="layui-form">
    <div class="layui-form layuimini-form">
        @if(isset($info->id))
        <input type="hidden" name="id" value="{{$info->id}}" />
        @endif

        <div class="layui-form-item">
            <label class="layui-form-label">分类</label>
            <div class="layui-input-block">
                <div id="articleCat"></div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">主标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" lay-verify="required" lay-reqtext="主标题不能为空" placeholder="请输入主标题" value="{{$info->title ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">副标题</label>
            <div class="layui-input-block">
                <input type="text" name="sub_title" placeholder="请输入副标题" value="{{$info->sub_title ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">缩略图</label>
            <div class="layui-input-block">
                <div class="layui-upload-drag" id="upload1">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                    <br>
                    <div class="{{$info->show_pic ? '' : 'layui-hide'}}" id="uploadShowImg">
                        <img src="{{$info->show_pic ?? ''}}" alt="上传成功后渲染" style="max-width: 196px">
                    </div>
                    <input type="hidden" name="pic" value="{{$info->pic ?? ''}}" />
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">摘要</label>
            <div class="layui-input-block">
                <textarea name="describe" placeholder="请输入摘要" class="layui-textarea">{{$info->describe ?? ''}}</textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">文章内容</label>
            <div class="layui-input-block">
                @include("admin.public.ueditor", ['content' => $info->content ?? ""])
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">属性</label>
            <div class="layui-input-block">
                <textarea name="attrs" placeholder="请输入属性" class="layui-textarea">{{$info->attrs ?? ''}}</textarea>
                <div style="font-size: 10px; color: red;">(以英文逗号分割)格式: 属性一 或者 属性一,属性二</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">标签</label>
            <div class="layui-input-block">
                <textarea name="tags" placeholder="请输入标签" class="layui-textarea">{{$info->tags ?? ''}}</textarea>
                <div style="font-size: 10px; color: red;">(以英文逗号分割)格式: 标签一 或者 标签一,标签二</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">来源</label>
            <div class="layui-input-block">
                <input type="text" name="source" placeholder="请输入source" value="{{$info->source ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">作者</label>
            <div class="layui-input-block">
                <input type="text" name="author" lay-verify="required" lay-reqtext="作者不能为空" placeholder="请输入作者" value="{{$info->author ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">关键词</label>
            <div class="layui-input-block">
                <input type="text" name="keys"  placeholder="请输入关键词" value="{{$info->keys ?? ''}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">(以英文逗号分割)格式: key1 或者 key1,key2</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">外链地址</label>
            <div class="layui-input-block">
                <input type="text" name="outer_chain"  placeholder="请输入外链地址" value="{{$info->outer_chain ?? ''}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">如果存在外链跳转，请输入以http://或者https://开头的域名全地址</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">虚拟浏览次数</label>
            <div class="layui-input-block">
                <input type="number" name="virtual_read" lay-verify="required" lay-reqtext="虚拟浏览次数不能为空" placeholder="请输入浏览次数" value="{{$info->virtual_read ?? 0}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">PS: 虚拟浏览次数只能为大于等于0。</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">虚拟点赞量</label>
            <div class="layui-input-block">
                <input type="number" name="virtual_agree" lay-verify="required" lay-reqtext="虚拟点赞量不能为空" placeholder="请输入虚拟点赞量" value="{{$info->virtual_agree ?? 0}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">PS: 虚拟点赞量只能为大于等于0。</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">虚拟收藏量</label>
            <div class="layui-input-block">
                <input type="number" name="virtual_favorite" lay-verify="required" lay-reqtext="虚拟收藏量不能为空" placeholder="请输入虚拟收藏量" value="{{$info->virtual_favorite ?? 0}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">PS: 虚拟收藏量只能为大于等于0。</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                @foreach($info->getStatusArr() as $k=>$v)
                    @if(isset($info->status))
                        <input type="radio" name="status" value="{{$k}}" title="{{$v}}" @if($k == $info->status) checked="" @endif />
                    @else
                        <input type="radio" name="status" value="{{$k}}" title="{{$v}}" @if($k == 1) checked="" @endif />
                    @endif
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" lay-verify="required" lay-reqtext="排序不能为空" placeholder="请输入排序" value="{{$info->sort ?? 100}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">PS: 排序值只能为大于等于0 ~ 小于等于100。</div>
            </div>
        </div>

        <div class="hr-line"></div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" id="saveBtn" lay-submit lay-filter="saveBtn">保存</button>
            </div>
        </div>

    </div>
    </form>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['iconPickerFa', 'form', 'layer', 'upload', 'xmSelect'], function () {
            var iconPickerFa = layui.iconPickerFa,
                form = layui.form,
                layer = layui.layer,
                upload = layui.upload,
                xmSelect = layui.xmSelect,
                $ = layui.$;

            //拖拽上传
            upload.render({
                elem: '#upload1'
                ,url: '/admin/upload/upload' //改成您自己的上传接口
                ,accept: 'images'
                ,acceptMime: 'image/*'
                ,size: 400 //限制文件大小，单位 KB
                ,headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                ,done: function(res){
                    if(res.code==0){
                        layer.msg("上传成功",{icon: 1});
                        var domain = window.location.host;
                        layui.$('#uploadShowImg').removeClass('layui-hide').find('img').attr('src', "http://" + domain + "/" + res.data[0]);
                        $("input[name='pic']").val(res.data[0]);
                    }else{
                        layer.msg(res.message,{icon: 2});
                        layui.$('#uploadShowImg').addClass('layui-hide');
                        $("input[name='pic']").val('');
                    }
                }
            });

            // 渲染属性多选
            function setXmSelect(id, name, data){
                xmSelect.render({
                    el: '#' + id,
                    // layVerify: 'required',
                    name:name,
                    toolbar: {
                        show: true,
                    },
                    filterable: true,
                    theme: {
                        color: '#a5673f',
                    },
                    tree: {
                        //是否显示树状结构
                        show: true,
                        //是否展示三角图标
                        showFolderIcon: true,
                        //是否显示虚线
                        showLine: false,
                        //间距
                        indent: 20,
                        //默认展开节点的数组, 为 true 时, 展开所有节点
                        expandedKeys: [],
                        //是否严格遵守父子模式
                        strict: false,
                        //是否开启极简模式
                        simple: false,
                        //点击节点是否展开
                        clickExpand: true,
                        //点击节点是否选中
                        clickCheck: true,
                    },
                    data: data
                })
            }

            var infoId = $('input[name="id"]').val();
            $.ajax({
                url:'/admin/article/xmSelect',
                type:'post',
                data:{'id':infoId},
                dataType:'JSON',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success:function(res){
                    console.log(res);
                    if(res.code==0){
                        setXmSelect("articleCat", "cat_ids", res.data.articleCat);
                    }else{
                        layer.msg(res.message,{icon: 2});
                    }
                },
                error:function (data) {
                    layer.msg(res.message,{icon: 2});
                }
            });

            //监听提交
            form.on('submit(saveBtn)', function(data){
                $("#saveBtn").addClass("layui-btn-disabled");
                $("#saveBtn").attr('disabled', 'disabled');
                $.ajax({
                    url:'/admin/article/edit',
                    type:'post',
                    data:data.field,
                    dataType:'JSON',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(res){
                        if(res.code==0){
                            // var index = parent.layer.getFrameIndex(window.name);
                            // layer.msg(res.message,{icon: 1},function (){
                            //     parent.layer.close(index)
                            // });
                            layer.msg(res.message,{icon: 1},function (){
                                // parent.layer.close(index)
                                parent.location.reload();
                            });
                        }else{
                            layer.msg(res.message,{icon: 2});
                            $("#saveBtn").removeClass("layui-btn-disabled");
                            $("#saveBtn").removeAttr('disabled');
                        }
                    },
                    error:function (data) {
                        layer.msg(res.message,{icon: 2});
                        $("#saveBtn").removeClass("layui-btn-disabled");
                        $("#saveBtn").removeAttr('disabled');
                    }
                });
            });
        });
    </script>
@endsection