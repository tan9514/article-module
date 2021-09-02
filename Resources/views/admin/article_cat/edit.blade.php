@extends('admin.public.header')
@section('title',$title)
@section('listcontent')
    <div class="layui-form layuimini-form">
        @if(isset($info->id))
        <input type="hidden" name="id" value="{{$info->id}}" />
        @endif

        <div class="layui-form-item">
            <label class="layui-form-label">上级分类</label>
            <div class="layui-input-block">
                <select name="cat_pid" lay-search>
                    <option value="">顶级分类</option>
                    <?php
                        function setOptionList($optionList, $num, $infoCatPid, $infoId){
                            $optionTest = "|";
                            for ($nu = 0; $nu < $num; $nu++){
                                $optionTest .= "—";
                            }
                            foreach ($optionList as $optionItem){
                                if($infoId > 0 && ($optionItem["cat_pid"] == $infoId || $optionItem["id"] == $infoId)) continue;
                                $selected = "";
                                if($optionItem["id"] == $infoCatPid) $selected = "selected";
                                echo "<option value='".$optionItem["id"]."' ".$selected." >".$optionTest.$optionItem["name"]."</option>";
                                if(isset($optionItem["list"]) && !empty($optionItem["list"])){
                                    setOptionList($optionItem["list"], ++$num, $infoCatPid, $infoId);
                                }
                            }
                        }
                        setOptionList($catArr, 1, isset($info->cat_pid) ? $info->cat_pid : 0, isset($info->id) ? $info->id : 0);
                    ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">分类名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" lay-reqtext="分类名称不能为空" placeholder="请输入分类名称" value="{{$info->name ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">分类副名称</label>
            <div class="layui-input-block">
                <input type="text" name="sub_name" placeholder="请输入分类副名称" value="{{$info->sub_name ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">缩略图</label>
            <div class="layui-input-block">
                <div class="layui-upload-drag" id="upload1">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                    <br>
                    <div class="{{$info->show_cat_pic ? '' : 'layui-hide'}}" id="uploadShowImg">
                        <img src="{{$info->show_cat_pic ?? ''}}" alt="上传成功后渲染" style="max-width: 196px">
                    </div>
                    <input type="hidden" name="cat_pic" value="{{$info->cat_pic ?? ''}}" />
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <textarea name="cat_describe" placeholder="请输入描述" class="layui-textarea">{{$info->cat_describe ?? ''}}</textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">内容</label>
            <div class="layui-input-block">
                <textarea name="cat_content" placeholder="请输入内容" class="layui-textarea">{{$info->cat_content ?? ''}}</textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">SEO</label>
            <div class="layui-input-block">
                <input type="text" name="cat_seo"  placeholder="请输入SEO" value="{{$info->cat_seo ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">关键词</label>
            <div class="layui-input-block">
                <input type="text" name="cat_keys"  placeholder="请输入关键词" value="{{$info->cat_keys ?? ''}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">关键词，格式: key1 或者 key1,key2</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">外链地址</label>
            <div class="layui-input-block">
                <input type="text" name="cat_outer_chain"  placeholder="请输入外链地址" value="{{$info->cat_outer_chain ?? ''}}" class="layui-input" />
                <div style="font-size: 10px; color: red;">如果存在外链跳转，请输入以http://或者https://开头的域名全地址</div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">推荐</label>
            <div class="layui-input-block">
                @foreach($info->getRecommendArr() as $k=>$v)
                    <input type="radio" name="is_recommend" value="{{$k}}" title="{{$v}}" @if($k == $info->is_recommend) checked="" @endif />
                @endforeach
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
                <div style="font-size: 10px; color: red;">排序值只能为大于等于0 ~ 小于等于100。</div>
            </div>
        </div>

        <div class="hr-line"></div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" id="saveBtn" lay-submit lay-filter="saveBtn">保存</button>
            </div>
        </div>

    </div>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['iconPickerFa', 'form', 'layer', 'upload'], function () {
            var iconPickerFa = layui.iconPickerFa,
                form = layui.form,
                layer = layui.layer,
                upload = layui.upload,
                $ = layui.$;

            //拖拽上传
            upload.render({
                elem: '#upload1'
                ,url: '/admin/upload/upload' //改成您自己的上传接口
                ,accept: 'images'
                ,acceptMime: 'image/*'
                ,size: 300 //限制文件大小，单位 KB
                ,headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                ,done: function(res){
                    if(res.code==0){
                        layer.msg("上传成功",{icon: 1});
                        var domain = window.location.host;
                        layui.$('#uploadShowImg').removeClass('layui-hide').find('img').attr('src', "http://" + domain + "/" + res.data[0]);
                        $("input[name='cat_pic']").val(res.data[0]);
                    }else{
                        layer.msg(res.message,{icon: 2});
                        layui.$('#uploadShowImg').addClass('layui-hide');
                        $("input[name='cat_pic']").val('');
                    }
                }
            });

            //监听提交
            form.on('submit(saveBtn)', function(data){
                $("#saveBtn").addClass("layui-btn-disabled");
                $("#saveBtn").attr('disabled', 'disabled');
                $.ajax({
                    url:'/admin/article_cat/edit',
                    type:'post',
                    data:data.field,
                    dataType:'JSON',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(res){
                        if(res.code==0){
                            // setTimeout('parent.location.reload()',2000);
                            // var index = parent.layer.getFrameIndex(window.name);
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