/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
$(document).ready(function() {
    // Open fancy box for updating all urls at a time
    $('#update-all-urls').on('click', function(event) {
        event.preventDefault();
        $.fancybox.open({
            closeClick: false, // prevents closing when clicking INSIDE fancybox 
            href: controller_url,
            type: "ajax",
            openEffect: 'none',
            closeEffect: 'none',
            helpers: {
                overlay: { closeClick: false } // prevents closing when clicking OUTSIDE fancybox 
            },
            ajax: {
                type: "POST",
                dataType: "json",
                data: {
                    ajax: true,
                    action: 'openFancyBoxForAllUrls',
                }
            }
        });
    });
});

// Submit data for new urls against pagenofound
$(document).on('submit', "#submit-new-urls", function(event) {
    event.preventDefault(); //prevent default action 
    var post_url = $(this).attr("action"); //get form action url
    var request_method = $(this).attr("method"); //get form GET/POST method
    var form_data = new FormData(this); //Creates new FormData object
    $.ajax({
        url: post_url,
        type: request_method,
        dataType: "json",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false
    }).done(function(response) {
        console.log(response);
        if (response['success'] == true) {
            showSuccessMessage('Successfully Updated');
            location.reload();
        } else {
            showErrorMessage(response['msg']);
        }
    });
});