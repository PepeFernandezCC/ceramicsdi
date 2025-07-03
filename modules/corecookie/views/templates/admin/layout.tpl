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


<div id="master">
	<div id="ajax-load" class="hidden"><div></div></div>
	<div id="ajax-load-google">
		<div class="core-google-loader">
			<div class="core-google-loader-dot core-google-loader-blue"></div>
			<div class="core-google-loader-dot core-google-loader-red"></div>
			<div class="core-google-loader-dot core-google-loader-yellow"></div>
			<div class="core-google-loader-dot core-google-loader-green"></div>
		</div>
	</div>
	<div id="core-flash-msg" class="core-flash-msg core-xdot"></div>
	<div id='bcanvas' class='core-bcanvas'>
		<div class='-in'>
			<div class='core-close' onclick="BC.hide();">
				<span class='icon-remove'></span>
			</div>

			<div class='__main'>
				<div class='__content'>
					<div class='__canvas core-canvas'></div>
				</div>
			</div>
		</div>
	</div>

	{$header_html nofilter} {* This is html code so no need to escape *}
	<div id='core-apps'></div>
	<div id='core-video'></div>
	<div id="apdialogs"></div>
	<div id="offcanvas-flip" class="uk-offcanvas uk-offcanvas-flip"></div>
	<div id='page'>
		{$content nofilter} {* This is html code so no need to escape *}
	</div>
</div>

<script>
	Core.initLayout();
</script>
