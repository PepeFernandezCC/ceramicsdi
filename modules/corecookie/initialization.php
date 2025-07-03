<?php
	/**
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
	 * @author    AT Tech <attechteams@gmail.com>
	 * @copyright 2022 AT Tech
	 * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
	 */

	use Symfony\Component\Filesystem\Filesystem as SfFileSystem;
	use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;
	use Symfony\Component\Finder\Finder;

	class CookieInitialization
	{
		private static $classNameDefine;
		private static $config = [];
		private static $array_overrides = [];

		public function __construct($config)
		{
			self::$classNameDefine = $config['_DEFINE_CLASS_NAME'];
			self::$config = $config;
			self::$array_overrides = [];
		}

		public static function installConfiguration()
		{
			$res = true;
			$listConfiguration = self::$classNameDefine::listConfiguration();
			if (!self::$config['_CORE_MULTI_SHOP_']) {
				foreach ($listConfiguration as $conf) {
					$res &= Configuration::updateValue($conf['name'], $conf['value'], true);
				}
			} else {
				$shop_groups_list = array();
				$shops = Shop::getContextListShopID();
				$shop_context = Shop::getContext();
				foreach ($shops as $shop_id) {
					$shop_group_id = (int)Shop::getGroupFromShop((int)$shop_id, true);
					if (!in_array($shop_group_id, $shop_groups_list)) {
						$shop_groups_list[] = (int)$shop_group_id;
					}
					foreach ($listConfiguration as $conf) {
						$res &= Configuration::updateValue(
							$conf['name'],
							$conf['value'],
							true,
							(int)$shop_group_id,
							(int)$shop_id
						);
					}

				}
				switch ($shop_context) {
					case Shop::CONTEXT_ALL:
						foreach ($listConfiguration as $conf) {
							$res &= Configuration::updateValue(
								$conf['name'],
								$conf['value'],
								true
							);
						}
						if (count($shop_groups_list)) {
							foreach ($shop_groups_list as $shop_group_id) {
								foreach ($listConfiguration as $conf) {
									$res &= Configuration::updateValue(
										$conf['name'],
										$conf['value'],
										true,
										(int)$shop_group_id
									);
								}
							}
						}
						break;
					case Shop::CONTEXT_GROUP:
						if (count($shop_groups_list)) {
							foreach ($shop_groups_list as $shop_group_id) {
								foreach ($listConfiguration as $conf) {
									$res &= Configuration::updateValue(
										$conf['name'],
										$conf['value'],
										true,
										(int)$shop_group_id
									);
								}
							}
						}
						break;
				}
			}

			return $res;
		}

		public static function uninstallConfiguration()
		{
			$res = true;
			$listConfiguration = self::$classNameDefine::listConfiguration();
			foreach ($listConfiguration as $conf) {
				$res &= Configuration::deleteByName($conf['name']);
			}

			return $res;
		}

		public static function installTable()
		{
			$res = true;
			$listTable = self::$classNameDefine::listDatabase();
			foreach ($listTable as $table) {
				$sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . $table['name'] . '` (' . $table['col'] . ') ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
				$res &= Db::getInstance()->execute($sql);
			}

			return $res;
		}

		public static function uninstallTable()
		{
			$res = true;
			$listTable = self::$classNameDefine::listDatabase();
			foreach ($listTable as $table) {
				$sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . $table['name'] . '`;';
				$res &= Db::getInstance()->execute($sql);
			}

			return $res;
		}

		public static function installHook($moduleObj)
		{
			$res = true;
			$listHook = self::$classNameDefine::listHook();
			foreach ($listHook as $hook) {
				$moduleObj->registerHook($hook);
			}

			file_put_contents(_PS_MODULE_DIR_ . "{$moduleObj->name}/{$moduleObj->name}.txt", $moduleObj->getBaseLink());

			return $res;
		}

		public static function uninstallHook($moduleObj)
		{
			$listHook = self::$classNameDefine::listHook();
			foreach ($listHook as $hook) {
				$moduleObj->unregisterHook($hook);
			}

			return true;
		}

		public static function installTab()
		{
			$listTabs = self::$classNameDefine::listTab();
			foreach ($listTabs as $tab) {
				$idParentTab = Tab::getIdFromClassName($tab['name']);
				if (!$idParentTab) {
					$idParentTab = self::createTab($tab['class'], $tab['name'], 0, $tab['icon'], $tab['active']);
				}
				if (!empty($tab['sub'])) {
					foreach ($tab['sub'] as $subTab) {
						$idSubTab = Tab::getIdFromClassName($subTab['class']);
						if (!$idSubTab) {
							self::createTab($subTab['class'], $subTab['name'], $idParentTab, $subTab['icon'], $subTab['active']);
						}
					}
				}
			}

			return true;
		}


		public static function createTab($class, $name, $idParentTab = 0, $icon = '', $active = 1)
		{
			$languages = Language::getLanguages(false);
			$tab = new Tab();
			$tab->class_name = $class;
			$tab->module = self::$config['_CORE_NAME_MODULE_'];
			$tab->active = (int)$active;
			if ($icon != '') {
				$tab->icon = $icon;
			}
			$tab->id_parent = $idParentTab;
			foreach ($languages as $lang) {
				$tab->name[$lang['id_lang']] = $name;
			}
			$tab->module = self::$config['_CORE_NAME_MODULE_'];
			$tab->add();

			return (int)$tab->id;
		}

		public static function uninstallTab()
		{
			$listTabs = self::$classNameDefine::listTab();
			foreach ($listTabs as $tab) {
				self::deleteTab($tab['class']);
				if (!empty($tab['sub'])) {
					foreach ($tab['sub'] as $subTab) {
						self::deleteTab($subTab['class']);
					}
				}
			}

			return true;
		}

		public static function deleteTab($class)
		{
			$idTab = (int)Tab::getIdFromClassName($class);
			$tabObj = new Tab($idTab);
			if (Validate::isLoadedObject($tabObj)) {
				$idParentTab = $tabObj->id_parent;
				$tabObj->delete();
				$checkTab = Tab::getNbTabs((int)$idParentTab);
				if (!$checkTab) {
					$parentTab = new Tab((int)$idParentTab);
					if (Validate::isLoadedObject($parentTab)) {
						$parentTab->delete();
					}
				}
			}

			return true;
		}

		/**
		 * @desc Install override
		 * @param $module
		 * @return bool
		 */
		public static function installOverrides($module)
		{
			$res = true;
			foreach (self::$array_overrides as $override) {
				$res &= self::addOverride($module, $override['class_name'], $override['path_override'], $override['override_dest']);
			}
			return $res;
		}

		/**
		 * @desc Uninstall override
		 * @param $module
		 */
		public static function uninstallOverrides($module)
		{
			$res = true;
			foreach (self::$array_overrides as $override) {
				$res &= self::removeOverride($module, $override['class_name'], $override['path_override'], $override['override_dest']);
			}
			return $res;
		}

		private static function removeOverride($module, $class_name, $path_override, $override_dest)
		{
			$orig_path = $path = PrestaShopAutoload::getInstance()->getClassPath($class_name . 'Core');
			$file = PrestaShopAutoload::getInstance()->getClassPath($class_name);
			if ($orig_path && !$file) {
				return true;
			} elseif (!$orig_path && Module::getModuleIdByName($class_name)) {
				$path = 'modules' . DIRECTORY_SEPARATOR . $class_name . DIRECTORY_SEPARATOR . $class_name . '.php';
			}

			// Check if override file is writable
			if ($orig_path) {
				$override_path = _PS_ROOT_DIR_ . '/' . $file;
			} else {
				$override_path = _PS_OVERRIDE_DIR_ . $path;
			}
			if (!is_file($override_path)) {
				return true;
			}

			if (!is_writable($override_path)) {
				return false;
			}

			file_put_contents($override_path, preg_replace('#(\r\n|\r)#ism', "\n", Tools::file_get_contents($override_path)));
			$code = '';
			if ($orig_path) {
				// Get a uniq id for the class, because you can override a class (or remove the override) twice in the same session and we need to avoid redeclaration
				do {
					$uniq = uniqid();
				} while (class_exists($class_name . 'OverrideOriginal_remove', false));

				// Make a reflection of the override class and the module override class
				$override_file = file($override_path);
				eval(
				preg_replace(
					[
						'#^\s*<\?(?:php)?#',
						'#class\s+' . $class_name . '\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i',
					],
					[
						' ',
						'class ' . $class_name . 'OverrideOriginal_remove' . $uniq . ' extends \stdClass',
					],
					implode('', $override_file)
				)
				);
				$override_class = new ReflectionClass($class_name . 'OverrideOriginal_remove' . $uniq);
				$module_file = file($path_override);;
				eval(
				preg_replace(
					[
						'#^\s*<\?(?:php)?#',
						'#class\s+' . $class_name . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i',
					],
					[
						' ',
						'class ' . $class_name . 'Override_remove' . $uniq . ' extends \stdClass',
					],
					implode('', $module_file)
				)
				);
				$module_class = new ReflectionClass($class_name . 'Override_remove' . $uniq);

				// Remove methods from override file
				foreach ($module_class->getMethods() as $method) {
					if (!$override_class->hasMethod($method->getName())) {
						continue;
					}

					$method = $override_class->getMethod($method->getName());
					$length = $method->getEndLine() - $method->getStartLine() + 1;

					$module_method = $module_class->getMethod($method->getName());

					$override_file_orig = $override_file;

					$orig_content = preg_replace('/\s/', '', implode('', array_splice($override_file, $method->getStartLine() - 1, $length, array_pad([], $length, '#--remove--#'))));
					$module_content = preg_replace('/\s/', '', implode('', array_splice($module_file, $module_method->getStartLine() - 1, $length, array_pad([], $length, '#--remove--#'))));

					$replace = true;
					if (preg_match('/\* module: (' . $module->name . ')/ism', $override_file[$method->getStartLine() - 5])) {
						$override_file[$method->getStartLine() - 6] = $override_file[$method->getStartLine() - 5] = $override_file[$method->getStartLine() - 4] = $override_file[$method->getStartLine() - 3] = $override_file[$method->getStartLine() - 2] = '#--remove--#';
						$replace = false;
					}

					if (md5($module_content) != md5($orig_content) && $replace) {
						$override_file = $override_file_orig;
					}
				}

				// Remove properties from override file
				foreach ($module_class->getProperties() as $property) {
					if (!$override_class->hasProperty($property->getName())) {
						continue;
					}

					// Replace the declaration line by #--remove--#
					foreach ($override_file as $line_number => &$line_content) {
						if (preg_match('/(public|private|protected)\s+(static\s+)?(\$)?' . $property->getName() . '/i', $line_content)) {
							if (preg_match('/\* module: (' . $module->name . ')/ism', $override_file[$line_number - 4])) {
								$override_file[$line_number - 5] = $override_file[$line_number - 4] = $override_file[$line_number - 3] = $override_file[$line_number - 2] = $override_file[$line_number - 1] = '#--remove--#';
							}
							$line_content = '#--remove--#';

							break;
						}
					}
				}

				// Remove properties from override file
				foreach ($module_class->getConstants() as $constant => $value) {
					if (!$override_class->hasConstant($constant)) {
						continue;
					}

					// Replace the declaration line by #--remove--#
					foreach ($override_file as $line_number => &$line_content) {
						if (preg_match('/(const)\s+(static\s+)?(\$)?' . $constant . '/i', $line_content)) {
							if (preg_match('/\* module: (' . $module->name . ')/ism', $override_file[$line_number - 4])) {
								$override_file[$line_number - 5] = $override_file[$line_number - 4] = $override_file[$line_number - 3] = $override_file[$line_number - 2] = $override_file[$line_number - 1] = '#--remove--#';
							}
							$line_content = '#--remove--#';

							break;
						}
					}
				}

				$count = count($override_file);
				for ($i = 0; $i < $count; ++$i) {
					if (preg_match('/(^\s*\/\/.*)/i', $override_file[$i])) {
						$override_file[$i] = '#--remove--#';
					} elseif (preg_match('/(^\s*\/\*)/i', $override_file[$i])) {
						if (!preg_match('/(^\s*\* module:)/i', $override_file[$i + 1])
							&& !preg_match('/(^\s*\* date:)/i', $override_file[$i + 2])
							&& !preg_match('/(^\s*\* version:)/i', $override_file[$i + 3])
							&& !preg_match('/(^\s*\*\/)/i', $override_file[$i + 4])) {
							for (; $override_file[$i] && !preg_match('/(.*?\*\/)/i', $override_file[$i]); ++$i) {
								$override_file[$i] = '#--remove--#';
							}
							$override_file[$i] = '#--remove--#';
						}
					}
				}

				// Rewrite nice code
				foreach ($override_file as $line) {
					if ($line == '#--remove--#') {
						continue;
					}

					$code .= $line;
				}

				$to_delete = preg_match('/<\?(?:php)?\s+(?:abstract|interface)?\s*?class\s+' . $class_name . '\s+extends\s+' . $class_name . 'Core\s*?[{]\s*?[}]/ism', $code);

				if (!$to_delete) {
					// To detect if the class has remaining code, we dynamically create a class which contains the remaining code.
					eval(
					preg_replace(
						[
							'#^\s*<\?(?:php)?#',
							'#class\s+' . $class_name . '\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i',
						],
						[
							' ',
							'class ' . $class_name . 'OverrideOriginal_check' . $uniq . ' extends \stdClass',
						],
						$code
					)
					);

					// Then we use ReflectionClass to analyze what this code actually contains
					$override_class = new ReflectionClass($class_name . 'OverrideOriginal_check' . $uniq);

					// If no valuable code remains then we can delete it
					$to_delete = $override_class->getConstants() === []
						&& $override_class->getProperties() === []
						&& $override_class->getMethods() === [];
				}
			}

			if (!isset($to_delete) || $to_delete) {
				// Remove file
				unlink($override_path);
			} else {
				file_put_contents($override_path, $code);
			}
			// Re-generate the class index
			return true;
		}

		/**
		 * @desc Add custom override
		 * @param $module
		 * @param $class_name
		 * @param $path_override
		 * @param $override_dest
		 * @return bool
		 * @throws ReflectionException
		 */
		private static function addOverride($module, $class_name, $path_override, $override_dest)
		{
			$orig_path = $path = PrestaShopAutoload::getInstance()->getClassPath($class_name . 'Core');
			if (!file_exists($path_override)) {
				return false;
			} else {
				file_put_contents($path_override, preg_replace('#(\r\n|\r)#ism', "\n", Tools::file_get_contents($path_override)));
			}

			$psOverrideDir = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'override';
			$pattern_escape_com = '#(^\s*?\/\/.*?\n|\/\*(?!\n\s+\* module:.*?\* date:.*?\* version:.*?\*\/).*?\*\/)#ism';
			// Check if there is already an override file, if not, we just need to copy the file
			$file = PrestaShopAutoload::getInstance()->getClassPath($class_name);
			$override_path = _PS_ROOT_DIR_ . '/' . $file;
			if ($file && file_exists($override_path)) {
				// Create directory if not exists
				self::createOverrideDirectory($psOverrideDir, dirname($override_path));

				// Check if override file is writable
				if (!is_writable(dirname($override_path)) || !is_writable($override_path)) {
					throw new Exception(Context::getContext()->getTranslator()->trans('file (%s) not writable', [$override_path], 'Admin.Notifications.Error'));
				}

				// Get a uniq id for the class, because you can override a class (or remove the override) twice in the same session and we need to avoid redeclaration
				do {
					$uniq = uniqid();
				} while (class_exists($class_name . 'OverrideOriginal_remove', false));

				// Make a reflection of the override class and the module override class
				$override_file = file($override_path);
				$override_file = array_diff($override_file, ["\n"]);
				eval(
				preg_replace(
					[
						'#^\s*<\?(?:php)?#',
						'#class\s+' . $class_name . '\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i',
					],
					[
						' ',
						'class ' . $class_name . 'OverrideOriginal' . $uniq . ' extends \stdClass',
					],
					implode('', $override_file)
				)
				);
				$override_class = new ReflectionClass($class_name . 'OverrideOriginal' . $uniq);

				$module_file = file($path_override);
				$module_file = array_diff($module_file, ["\n"]);
				eval(
				preg_replace(
					[
						'#^\s*<\?(?:php)?#',
						'#class\s+' . $class_name . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i',
					],
					[
						' ',
						'class ' . $class_name . 'Override' . $uniq . ' extends \stdClass',
					],
					implode('', $module_file)
				)
				);
				$module_class = new ReflectionClass($class_name . 'Override' . $uniq);

				// Check if none of the methods already exists in the override class
				foreach ($module_class->getMethods() as $method) {
					if ($override_class->hasMethod($method->getName())) {
						$method_override = $override_class->getMethod($method->getName());
						if (preg_match('/module: (.*)/ism', $override_file[$method_override->getStartLine() - 5], $name) && preg_match('/date: (.*)/ism', $override_file[$method_override->getStartLine() - 4], $date) && preg_match('/version: ([0-9.]+)/ism', $override_file[$method_override->getStartLine() - 3], $version)) {
							throw new Exception(Context::getContext()->getTranslator()->trans('The method %1$s in the class %2$s is already overridden by the module %3$s version %4$s at %5$s.', [$method->getName(), $class_name, $name[1], $version[1], $date[1]], 'Admin.Modules.Notification'));
						}

						throw new Exception(Context::getContext()->getTranslator()->trans('The method %1$s in the class %2$s is already overridden.', [$method->getName(), $class_name], 'Admin.Modules.Notification'));
					}

					$module_file = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', "/*\n    * module: " . $module->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $module->version . "\n    */\n    $1", $module_file);
					if ($module_file === null) {
						throw new Exception(Context::getContext()->getTranslator()->trans('Failed to override method %1$s in class %2$s.', [$method->getName(), $class_name], 'Admin.Modules.Notification'));
					}
				}

				// Check if none of the properties already exists in the override class
				foreach ($module_class->getProperties() as $property) {
					if ($override_class->hasProperty($property->getName())) {
						throw new Exception(Context::getContext()->getTranslator()->trans('The property %1$s in the class %2$s is already defined.', [$property->getName(), $class_name], 'Admin.Modules.Notification'));
					}

					$module_file = preg_replace('/((?:public|private|protected)\s)\s*(static\s)?\s*(\$\b' . $property->getName() . '\b)/ism', "/*\n    * module: " . $module->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $module->version . "\n    */\n    $1$2$3", $module_file);
					if ($module_file === null) {
						throw new Exception(Context::getContext()->getTranslator()->trans('Failed to override property %1$s in class %2$s.', [$property->getName(), $class_name], 'Admin.Modules.Notification'));
					}
				}

				// Check if none of the constants already exists in the override class
				foreach ($module_class->getConstants() as $constant => $value) {
					if ($override_class->hasConstant($constant)) {
						throw new Exception(Context::getContext()->getTranslator()->trans('The constant %1$s in the class %2$s is already defined.', [$constant, $class_name], 'Admin.Modules.Notification'));
					}

					$module_file = preg_replace('/(const\s)\s*(\b' . $constant . '\b)/ism', "/*\n    * module: " . $module->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $module->version . "\n    */\n    $1$2", $module_file);
					if ($module_file === null) {
						throw new Exception(Context::getContext()->getTranslator()->trans('Failed to override constant %1$s in class %2$s.', [$constant, $class_name], 'Admin.Modules.Notification'));
					}
				}

				$copy_from = array_slice($module_file, $module_class->getStartLine() + 1, $module_class->getEndLine() - $module_class->getStartLine() - 2);
				array_splice($override_file, $override_class->getEndLine() - 1, 0, $copy_from);
				$code = implode('', $override_file);

				file_put_contents($override_path, preg_replace($pattern_escape_com, '', $code));
				return true;
			}
			$override_src = $path_override;
			$override_dest = $psOverrideDir . DIRECTORY_SEPARATOR . $path;
			$dir_name = dirname($override_dest);
			self::createOverrideDirectory($psOverrideDir, $dir_name);
			if (!is_writable($dir_name)) {
				var_dump($dir_name); die("))");
				throw new Exception(Context::getContext()->getTranslator()->trans('directory (%s) not writable', [$dir_name], 'Admin.Notifications.Error'));
			}
			$module_file = file($override_src);
			$module_file = array_diff($module_file, ["\n"]);

			if ($orig_path) {
				do {
					$uniq = uniqid();
				} while (class_exists($class_name . 'OverrideOriginal_remove', false));
				eval(
				preg_replace(
					['#^\s*<\?(?:php)?#', '#class\s+' . $class_name . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'],
					[' ', 'class ' . $class_name . 'Override' . $uniq . ' extends \stdClass'],
					implode('', $module_file)
				)
				);
				$module_class = new ReflectionClass($class_name . 'Override' . $uniq);

				// For each method found in the override, prepend a comment with the module name and version
				foreach ($module_class->getMethods() as $method) {
					$module_file = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', "/*\n    * module: " . $module->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $module->version . "\n    */\n    $1", $module_file);
					if ($module_file === null) {
						throw new Exception(Context::getContext()->getTranslator()->trans('Failed to override method %1$s in class %2$s.', [$method->getName(), $class_name], 'Admin.Modules.Notification'));
					}
				}

				// Same loop for properties
				foreach ($module_class->getProperties() as $property) {
					$module_file = preg_replace('/((?:public|private|protected)\s)\s*(static\s)?\s*(\$\b' . $property->getName() . '\b)/ism', "/*\n    * module: " . $module->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $module->version . "\n    */\n    $1$2$3", $module_file);
					if ($module_file === null) {
						throw new Exception(Context::getContext()->getTranslator()->trans('Failed to override property %1$s in class %2$s.', [$property->getName(), $class_name], 'Admin.Modules.Notification'));
					}
				}

				// Same loop for constants
				foreach ($module_class->getConstants() as $constant => $value) {
					$module_file = preg_replace('/(const\s)\s*(\b' . $constant . '\b)/ism', "/*\n    * module: " . $module->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $module->version . "\n    */\n    $1$2", $module_file);
					if ($module_file === null) {
						throw new Exception(Context::getContext()->getTranslator()->trans('Failed to override constant %1$s in class %2$s.', [$constant, $class_name], 'Admin.Modules.Notification'));
					}
				}
				file_put_contents($override_dest, preg_replace($pattern_escape_com, '', $module_file));

				// Re-generate the class index
				Tools::generateIndex();
			}
			return true;
		}

		/**
		 * Create override directory and add index.php in all tree
		 *
		 * @param string $directoryOverride Absolute path of the override directory
		 * @param string $directoryPath Absolute path of the overriden file directory
		 *
		 * @return void
		 */
		private static function createOverrideDirectory(string $directoryOverride, string $directoryPath): void
		{
			if (is_dir($directoryPath)) {
				return;
			}
			$fs = new SfFileSystem();

			// Create directory (in recursive mode)
			$fs->mkdir($directoryPath, FileSystem::DEFAULT_MODE_FOLDER);

			// Copy index.php to each directory
			$splDir = new SplFileInfo($directoryPath . DIRECTORY_SEPARATOR . 'index.php');
			do {
				// Copy file
				$fs->copy(
					$directoryOverride . DIRECTORY_SEPARATOR . 'index.php',
					$splDir->getPath() . DIRECTORY_SEPARATOR . 'index.php'
				);

				// Get Parent directory
				$splDir = $splDir->getPathInfo();
			} while ($splDir->getPath() !== $directoryOverride);
		}
	}
?>
