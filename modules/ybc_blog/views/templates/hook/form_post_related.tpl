{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if $is_ps16}
<div class="from_product_post_is16">
    <div class="form-group">
        <label class="control-label col-lg-3" for="form_step6_post_product">
            <span> {l s='Related posts' mod='ybc_blog'} </span>
        </label>
        <div class="col-lg-9">
            <div class="col-xl-12 col-lg-12" id="form_step6_post_product_field">
                <div class="search search-with-icon">
                    <input id="form_step6_post_product" name="form_step6_post_product" class="form-control ac_input" placeholder="{l s='Search posts by title or ID' mod='ybc_blog'}" type="text" autocomplete="off" />
                    <input id="id_post_product" type="hidden" value="{$id_product|intval}" />
                </div>
            </div>
            <div id="related-posts"  class="col-xl-12 col-lg-12">
                {if $selected_posts}
                    {foreach from = $selected_posts item='post'}
                        <div class="related-post related-post-{$post.id_post|intval}">
                            <button type="button" class="btn btn-default remove_button" onclick="ybcDelPostRelated({$post.id_post|intval});" name="{$post.id_post|intval}">
                                <i class="icon-remove text-danger"></i>
                            </button>
                            <img src="{if $post.thumb}{$post.thumb|escape:'html':'UTF-8'}{else}{$post.image|escape:'html':'UTF-8'}{/if}" style="width:32px;" />
                            <a href="{$post.link|escape:'html':'UTF-8'}" target="_blank">
                                {$post.title|escape:'html':'UTF-8'}
                            </a>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>
</div>
{else}
    <div class="form-group mb-4">
        <h2>{l s='Related posts' mod='ybc_blog'}</h2>
        <div class="row">
            <div class="col-xl-12 col-lg-12" id="form_step6_post_product_field">
                <div class="search search-with-icon">
                    <input id="form_step6_post_product" name="form_step6_post_product" class="form-control ac_input" placeholder="{l s='Search posts by title or ID' mod='ybc_blog'}" type="text" autocomplete="off" />
                    <input id="id_post_product" type="hidden" value="{$id_product|intval}" />
                </div>
            </div>
            <div id="related-posts"  class="col-xl-12 col-lg-12">
                {if $selected_posts}
                    {foreach from = $selected_posts item='post'}
                        <div class="related-post related-post-{$post.id_post|intval}">
                            <button type="button" class="btn btn-default remove_button" onclick="ybcDelPostRelated({$post.id_post|intval});" name="{$post.id_post|intval}">
                                <i class="icon-remove text-danger"></i>
                            </button>
                            <img src="{if $post.thumb}{$post.thumb|escape:'html':'UTF-8'}{else}{$post.image|escape:'html':'UTF-8'}{/if}" style="width:32px;" />
                            <a href="{$post.link|escape:'html':'UTF-8'}" target="_blank">
                                {$post.title|escape:'html':'UTF-8'}
                            </a>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>
{/if}
<script type="text/javascript">
    var link_search_post ='{$link_search_post nofilter}';
    var confirm_del_post ='{l s='Do you want to delete this post?' mod='ybc_blog' js=1}';
    var xhr_post;
    {literal}
    $(document).ready(function(){
        if($('.from_product_post_is16').length){
            $('#product-informations .panel-footer').before($('.from_product_post_is16').html());
            $('.from_product_post_is16').remove();
        }
        $(document).on('blur','#form_step6_post_product',function(){
            $('.list_posts li.active').removeClass('active');
        });
        $(document).on('keyup','#form_step6_post_product',function(e){
            if((e.keyCode==13 || e.keyCode==38 || e.keyCode==40) && $('.list_posts').length)
            {
                if(e.keyCode==40)
                {
                    if($('.list_posts li.active').length==0)
                    {
                        $('.list_posts li:first').addClass('active');
                    }
                    else
                    {
                        var $li_active = $('.list_posts li.active');
                        $('.list_posts li.active').removeClass('active');
                        if($li_active.next('li').length)
                            $li_active.next('li').addClass('active');
                        else
                            $('.list_posts li:first').addClass('active');
                    }
                }
                if(e.keyCode==38)
                {
                    if($('.list_posts li.active').length==0)
                    {
                        $('.list_posts li:last').addClass('active');
                    }
                    else
                    {
                        var $li_active = $('.list_posts li.active');
                        $('.list_posts li.active').removeClass('active');
                        if($li_active.prev('li').length)
                            $li_active.prev('li').addClass('active');
                        else
                            $('.list_posts li:last').addClass('active');
                    }
                }
                if(e.keyCode==13)
                {
                    $('.list_posts li.active').click();
                }
            }
            else
            {
                if(xhr_post)
                    xhr_post.abort();
                $('#form_step6_post_product').next('.list_posts').remove();
                xhr = $.ajax({
                    type: 'POST',
                    headers: { "cache-control": "no-cache" },
                    url: link_search_post,
                    async: true,
                    cache: false,
                    dataType : "json",
                    data: 'searchPostByQuery=1&q='+$('#form_step6_post_product').val(),
                    success: function(json)
                    {
                        if(json.posts)
                        {
                            var $html ='<ul class="list_posts">';
                            $(json.posts).each(function(){
                                $html +='<li data-id_post="'+this.id_post+'"> <img src="'+(this.thumb ? this.thumb : this.image )+'" style="width:32px;" /> <a href="'+this.link+'" target="_blank">'+this.title+'</a> </li>';
                            });
                            $html +='</ul>';
                            if($('.list_posts').length)
                                $('.list_posts').remove();
                            $('#form_step6_post_product').after($html);
                            $('.list_posts li').hover(function(){ $('.list_posts li.active').removeClass('active'); $(this).addClass('active');});
                        }
                    }
                });
            }
        });
        $(document).on('click','.list_posts a',function(e){
           e.preventDefault();
        });
        $(document).on('click','.list_posts li',function(){
            var id_product = $('#id_post_product').val();
            var id_post = $(this).data('id_post');
            var post_name = $(this).html();
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: link_search_post,
                async: true,
                cache: false,
                dataType : "json",
                data:'submitAddPostRelatedProduct=1&id_product='+id_product+'&id_post='+id_post,
                success: function(json)
                {
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('#related-posts').append('<div class="related-post related-post-'+id_post+'"><button type="button" class="btn btn-default remove_button" onclick="ybcDelPostRelated('+id_post+');" name="'+id_post+'"><i class="icon-remove text-danger"></i></button>'+post_name+'</div>');
                        $('.list_posts').remove();
                        $('#form_step6_post_product').val('');
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                }
            });
        });
    });
    function ybcDelPostRelated(id_post)
    {
        if(confirm(confirm_del_post))
        {
            var id_product = $('#id_post_product').val();
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: link_search_post,
                async: true,
                cache: false,
                dataType : "json",
                data:'submitDeletePostProduct=1&id_post='+id_post+'&id_product='+id_product,
                success: function(json)
                {
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('.related-post-'+id_post).remove();
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                }
            });
        }
    }
    {/literal}
</script>