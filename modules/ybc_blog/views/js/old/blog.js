/**
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
 */
 var height_index_heading = 100;


$(document).on('hooksLoaded',function(){
    runowl();
});
$(document).ready(function(){
    $(document).on('change','#form_blog input[type="file"],.blog-managament-information input[type="file"]',function(){
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert(ybc_blog_invalid_file);
        }
        else
        {
            var file_name = $(this).val().replace('C:\\fakepath\\','');
            if($(this).closest('.upload_form_custom').find('.file_name').length)
            {
                $(this).closest('.upload_form_custom').find('.file_name').html(file_name);
            }
            else   
                $(this).closest('.upload_form_custom').append('<span class="file_name">'+file_name+'</span>'); 
            readURL(this);            
        }
        if($(this).next('.file_name').length>0)
        {
           $(this).next('.file_name').html($(this).val().replace('C:\\fakepath\\','')) 
        }
    });
    if ( $('.ybc-blog-rtl').length > 0 ){
        var rtl_blog = true;

    } else {
        var rtl_blog = false;
    }

    function loadLazi(){
        if($('img.lazyload').length>0)
        {
            $('img.lazyload').lazyload({
                load : function () {
                    $(this).parent().removeClass('ybc_item_img_ladyload');
                    $(this).parent().parent().removeClass('ybc_item_img_ladyload');
                    $(this).addClass('isload');
                },
                threshold: 100,
            });
        }
    }
    $( window ).on('load',function() {
      loadLazi();
    });
    loadLazi();

    $(document).on('click','.owl-next',function(){
        if($('img.lazyload').length>0)
        {
            $('img.lazyload').lazyload();
        }
    });
    $(window).on('scroll',function(){
         autoLoadBlog();
    });
    runowl();
    autoLoadBlog();
});
function autoLoadBlog()
{
    var container = '.ybc-blog-wrapper-blog-list.loadmore';
    if ($(container).length > 0 && $(container+" .blog-paggination a.next").length > 0 && !$(container+" .blog-paggination a.next").hasClass('active') && $(window).scrollTop() + $(window).height() >= $(container).offset().top + $(container).height() ) {
        $(container+" .blog-paggination a.next").addClass('active');
          $('.ets_blog_loading.autoload').addClass('active');
          $.ajax({
                url:  $(container+" .blog-paggination a.next").attr('href'),
                data: 'loadajax=1'+($('select[name="ybc_sort_by_posts"]').length ? '&ybc_sort_by_posts='+$('select[name="ybc_sort_by_posts"]').val():''),
                type: 'post',
                dataType: 'json',                
                success: function(json){
                    if(json.list_blog)
                        $('.ybc-blog-list').append(json.list_blog);
                    if(json.list_galleries)
                    {
                        $('.ybc-galley-list').append(json.list_galleries);
                        ybcBlogPrettyPhoto();
                    }
                    $('.blog-paggination').html(json.blog_paggination);
                    if($('img.lazyload').length>0)
                    {
                        $('img.lazyload').lazyload({
                            load : function () {
                                $(this).parent().removeClass('ybc_item_img_ladyload');
                                $(this).parent().parent().removeClass('ybc_item_img_ladyload');
                            },
                            threshold: 100,
                        });
                    }
                    $('.ets_blog_loading.autoload').removeClass('active');
                },
                error: function(error)
                {
                   
                }
        });         
    } 
}
function runowl()
{
    if ( $('.ybc_blog_rtl_mode').length > 0 ){
        var rtl_blog = true;

    } else {
        var rtl_blog = false;
    }

    if ($('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list:not(.slick-initialized)').length > 0)
    {
        var number_post_related_per_row = $('.ybc-blog-related-posts').attr('data-items');
        $('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list:not(.slick-initialized)').slick({
            slidesToShow: number_post_related_per_row,
            arrows: true,
            rtl: rtl_blog,
            dots: false,
            autoplay: false,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: number_post_related_per_row,
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 400,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
        $('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list').on('changed.etsowl.carousel', function(event) {
            if($('img.lazyload').length>0)
            {
                $('img.lazyload').lazyload();
            }
        });
    }
    if ($('.ybc_related_products_type_carousel.ybc_blog_carousel:not(.slick-initialized)').length > 0)
    {
        $('.ybc_related_products_type_carousel.ybc_blog_carousel:not(.slick-initialized)').slick({
            slidesToShow: 4,
            arrows: true,
            rtl: rtl_blog,
            dots: false,
            autoplay: false,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 4,
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 400,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
        $('.ybc-blog-related-posts.ybc_blog_carousel .ybc-blog-related-posts-list').on('changed.etsowl.carousel', function(event) {
            if($('img.lazyload').length>0)
            {
                $('img.lazyload').lazyload();
            }
        });
    }
    if ($('.ybc_blog_related_posts_type_carousel .blog_type_slider:not(.slick-initialized)').length > 0)
    {
        $('.ybc_blog_related_posts_type_carousel .blog_type_slider:not(.slick-initialized)').slick({
            dots: false,
            infinite: false,
            speed: 300,
            arrows: true,
            slidesToShow: number_post_related_per_row,
            slidesToScroll: 4,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: number_post_related_per_row,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        infinite: true,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
        $('.ybc_blog_related_posts_type_carousel .blog_type_slider').on('changed.etsowl.carousel', function(event) {
            if($('img.lazyload').length>0)
            {
                $('img.lazyload').lazyload();
            }
        });
    }
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if($(input).closest('.col-md-9').find('.thumb_post').length <= 0)
            {
                $(input).closest('.col-md-9').append('<div class="thumb_post"><img src="'+e.target.result+'"/> </div>');
            }
            else
            {
                $(input).closest('.col-md-9').find('.thumb_post img').eq(0).attr('src',e.target.result);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}


function addLazyLoadAfterSlider(){
    if( $('.slick-cloned .ybc_item_img_ladyload .isload').length ){
        $('.slick-cloned .ybc_item_img_ladyload .isload').parent().removeClass('ybc_item_img_ladyload');
    }
    $('img.lazyload').each(function(){
        $(this).lazyload({
        threshold: 100,
        afterLoad      : function (element) {
			$(this).parent().removeClass('ybc_item_img_ladyload');
            $(this).parent().parent().removeClass('ybc_item_img_ladyload');
            $(this).addClass('isload');
		}
    });
    });
}

// Hàm tính toán thời gian đọc từ HTML content
function calculateReadingTimeFromHTML(htmlContent) {
  if (!htmlContent) return null;

  // Tạo một element tạm để lấy text từ HTML
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = htmlContent;
  
  // Đếm từ
  const text = tempDiv.innerText.trim();
  const wordCount = text.split(/\s+/).filter(word => word.length > 0).length;

  // Tốc độ đọc trung bình
  const wordsPerMinute = 200;
  let readingTimeMinutes = wordCount / wordsPerMinute;

  // Đếm ảnh trong HTML
  const images = tempDiv.querySelectorAll("img").length;
  let imageSeconds = 0;
  if (images > 0) {
    imageSeconds += 12; // ảnh đầu
    imageSeconds += Math.min(images - 1, 9) * 11; // ảnh 2–10
    if (images > 10) {
      imageSeconds += (images - 10) * 3; // ảnh 11+
    }
  }

  // Tổng cộng (phút)
  readingTimeMinutes += imageSeconds / 60;

  // Làm tròn lên
  const roundedMinutes = Math.max(1, Math.ceil(readingTimeMinutes));

  return {
    words: wordCount,
    images,
    minutes: roundedMinutes,
    exactMinutes: readingTimeMinutes.toFixed(2)
  };
}

// Sự kiện DOMContentLoaded để tự động tính toán thời gian đọc
document.addEventListener('DOMContentLoaded', function() {
  // Kiểm tra xem có div với id 'estimated-reading-time' không
  const estimatedTimeElement = document.querySelector('#estimated-reading-time');

  if (estimatedTimeElement) {
    // Tìm div có class 'blog_description'
    const blogDescriptionElement = document.querySelector('.blog_description');
    
    if (blogDescriptionElement) {
      // Lấy toàn bộ HTML inner của blog_description
      const htmlContent = blogDescriptionElement.innerHTML;
      
      // Tính toán thời gian đọc
      const readingTimeData = calculateReadingTimeFromHTML(htmlContent);
      
      if (readingTimeData) {
        // Lấy label từ data attributes
        const minuteLabel = estimatedTimeElement.getAttribute('data-minute') || 'minute';
        const minutesLabel = estimatedTimeElement.getAttribute('data-minutes') || 'minutes';
        
        // Chọn label phù hợp dựa trên số phút
        const timeLabel = readingTimeData.minutes === 1 ? minuteLabel : minutesLabel;
        
        // Hiển thị thời gian đọc trong element estimated-reading-time
        estimatedTimeElement.innerHTML = '<span>Estimated Reading Time:</span> <span>' + readingTimeData.minutes + ' ' + timeLabel + '</span>';
        
        // Có thể thêm data attributes để sử dụng sau này
        estimatedTimeElement.setAttribute('data-reading-minutes', readingTimeData.minutes);
        estimatedTimeElement.setAttribute('data-word-count', readingTimeData.words);
        estimatedTimeElement.setAttribute('data-image-count', readingTimeData.images);
      }
    }
  }
});



