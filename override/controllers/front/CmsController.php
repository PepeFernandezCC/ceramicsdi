<?php
/**
 * FMM PrettyURLs
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2018 © Fmemodules All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   PrettyURLs
*/
class CmsController extends CmsControllerCore
{
	/*
    * module: prettyurls
    * date: 2023-05-08 11:52:06
    * version: 2.2.8
    */
    public function init()
	{
		$link_rewrite = Tools::safeOutput(urldecode(Tools::getValue('cms_rewrite')));
		$cms_pattern = '/.*?content\/([0-9]+)\-([_a-zA-Z0-9-\pL]*)/';
		preg_match($cms_pattern, $_SERVER['REQUEST_URI'], $url_array);
		if (isset($url_array[2]) && $url_array[2] != '') {
			$link_rewrite = $url_array[2];
		}
		$cms_category_rewrite 	= Tools::safeOutput(urldecode(Tools::getValue('cms_category_rewrite')));
		$cms_cat_pattern = '/.*?content\/category\/([0-9]+)\-([_a-zA-Z0-9-\pL]*)/';
		preg_match($cms_cat_pattern, $_SERVER['REQUEST_URI'], $url_cat_array);
		if (isset($url_cat_array[2]) && $url_cat_array[2] != '') {
			$cms_category_rewrite = $url_cat_array[2];
		}
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		if ($link_rewrite) {
			$sql = 'SELECT tl.id_cms
					FROM '._DB_PREFIX_.'cms_lang tl
					LEFT OUTER JOIN '._DB_PREFIX_.'cms_shop t ON (t.id_cms = tl.id_cms)
					WHERE tl.link_rewrite = \''.pSQL($link_rewrite).'\' AND tl.id_lang = '.(int)$id_lang.' AND t.id_shop = '.(int)$id_shop;
			$id_cms = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
			if ($id_cms != '') {
				$_POST['id_cms'] = $id_cms;
				$_GET['cms_rewrite'] = '';
			}
		} elseif ($cms_category_rewrite) {
			$sql = 'SELECT id_cms_category
					FROM '._DB_PREFIX_.'cms_category_lang
					WHERE link_rewrite = \''.pSQL($cms_category_rewrite).'\' AND id_lang = '.(int)$id_lang;
			$id_cms_category = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
			if ($id_cms_category != '') {
				$_GET['cms_category_rewrite'] = '';
				$_POST['id_cms_category'] = $id_cms_category;
			}
		}
		$allow_accented_chars = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		if ($allow_accented_chars > 0) {
			$id_cms = (int)Tools::getValue('id_cms');
			if ($id_cms <= 0) {
				$id = (int)$this->crawlDbForId($_GET['cms_rewrite']);
				if ($id > 0) {
					$_POST['id_cms'] = $id;
				}
			}
		}
		parent::init();
	}
	
	/*
    * module: prettyurls
    * date: 2023-05-08 11:52:06
    * version: 2.2.8
    */
    protected function crawlDbForId($rew)
	{
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		$sql = new DbQuery();
        $sql->select('`id_cms`');
        $sql->from('cms_lang');
		$sql->where('`id_lang` = '.(int)$id_lang.' AND `id_shop` = '.(int)$id_shop.' AND `link_rewrite` = "'.pSQL($rew).'"');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}
}