@extends('admin.public.header')
@section('title',$title)
@section('listcontent')
    <div class="layui-form layuimini-form">

        <div class="layui-form-item">
            <label class="layui-form-label">分类</label>
            <div class="layui-input-block">
                @foreach($info->catArr as $catInfo)
                    <span style='background-color: rgb(165, 103, 63); color: #fff; padding: height: 26px; line-height: 26px; display: inline-block; position: relative; padding: 0px 5px; margin: 2px 5px 2px 0; border-radius: 3px; align-items: baseline;'>{{$catInfo}}</span>
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">主标题</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->title}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">副标题</label>
            <div class="layui-input-block">
                <input type="text"  value="{{$info->sub_title}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">缩略图</label>
            <div class="layui-input-block">
                <div class="{{$info->show_pic ? '' : 'layui-hide'}}">
                    <img src="{{$info->show_pic}}" alt="缩略图" style="max-width: 196px">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">摘要</label>
            <div class="layui-input-block">
                <textarea class="layui-textarea" disabled >{{$info->describe}}</textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">文章内容</label>
            <div class="layui-input-block">
                @include("admin.public.ueditor", ['content' => $info->content ?? ""])
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">属性</label>
            <div class="layui-input-block">
                @foreach($info->attrArr as $catInfo)
                    <span style='background-color: rgb(165, 103, 63); color: #fff; padding: height: 26px; line-height: 26px; display: inline-block; position: relative; padding: 0px 5px; margin: 2px 5px 2px 0; border-radius: 3px; align-items: baseline;'>{{$catInfo}}</span>
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">标签</label>
            <div class="layui-input-block">
                @foreach($info->tagArr as $catInfo)
                    <span style='background-color: rgb(165, 103, 63); color: #fff; padding: height: 26px; line-height: 26px; display: inline-block; position: relative; padding: 0px 5px; margin: 2px 5px 2px 0; border-radius: 3px; align-items: baseline;'>{{$catInfo}}</span>
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">来源</label>
            <div class="layui-input-block">
                <input type="text"  value="{{$info->source}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">作者</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->author}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">关键词</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->keys}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">外链地址</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->outer_chain}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">浏览次数</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->read}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">点赞量</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->agree}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">收藏量</label>
            <div class="layui-input-block">
                <input type="text" value="{{$info->favorite}}" class="layui-input" disabled />
            </div>
        </div>

    </div>
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

            //监听提交
            form.on('submit(saveBtn)', function(data){
                return false;
            });
        });
    </script>
@endsection