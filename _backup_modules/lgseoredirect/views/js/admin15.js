/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$(document).ready(function(){
	$('#content').addClass('bootstrap');
	$('.nav li a').click(function(){
		 $('.nav li').removeClass('active');
		 $('.tab-pane').removeClass('active');
		 $($(this).attr('href')).addClass('active');
		 $(this).closest('li').addClass('active');
		 return false;
	});
	$('.lang-btn').click(function(){
		$(this).closest('div').addClass('open')
	});
});
function hideOtherLanguage(id)
{
	$('.translatable-field').hide();
	$('.lang-' + id).show();

	var id_old_language = id_language;
	id_language = id;

}
