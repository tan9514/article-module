@extends('admin.public.header')
@section('title',$title)
@section('listcontent')
    <form class="layui-form">
    <div class="layui-form layuimini-form">
        <div class="layui-form-item">
            <label class="layui-form-label">勾选的文章</label>
            <div class="layui-input-block">
                @foreach($infos as $it)
                <input type="text" value="{{$it ?? ''}}" class="layui-input" style="margin-bottom: 5px;" disabled />
                @endforeach
            </div>
        </div>

        <input type="hidden" name="ids" value="{{$id}}" />

        <div class="layui-form-item">
            <label class="layui-form-label">分类</label>
            <div class="layui-input-block">
                <div id="articleCat"></div>
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

            $.ajax({
                url:'/admin/article/xmSelect',
                type:'post',
                data:{},
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
                    url:'/admin/article/batchEdit',
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