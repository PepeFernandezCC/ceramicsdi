{**
* NOTICE OF LICENSE
*
* This source file is subject to the Commercial License and is not open source.
* Each license that you purchased is only available for 1 website only.
* You can't distribute, modify or sell this code.
* If you want to use this file on more websites, you need to purchase additional licenses.
*
* DISCLAIMER
*
* Do not edit or add to this file.
* If you need help please contact <attechteams@gmail.com>
*
* @author    Alpha Tech <attechteams@gmail.com>
* @copyright 2022 Alpha Tech
* @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{extends file='customer/page.tpl'}
{block name='page_content'}
    <style>
        div#core-cookie-compliance {
            background: #ffffff;
            padding: 20px;
            border-radius: 6px;
        }
    </style>
    <div id="core-cookie-compliance">{$content_page nofilter}</div> {* This is html code so no need to escape *}
{/block}
