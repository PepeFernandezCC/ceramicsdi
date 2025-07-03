<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */

$query3 = "SELECT count(name) as c FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_configuration WHERE name='GDPR'";
$record3 = Db::getInstance()->getRow($query3);

if (isset($record3['c']) && $record3['c'] == 0) {
    $module->installTab('AdminCorreosOficialNotifications', $module->l('Notifications', 'correosoficial'), 'AdminCorreosOficialCronProcess') &&
    $module->registerHook('actionCorreosAdminControllers') &&
    $module->registerHook('displayAdminAfterHeader') &&
    Db::getInstance()->insert('correos_oficial_configuration', ['name' => 'GDPR', 'value' => 0, 'type' => 'analitica']) &&
    Db::getInstance()->insert('correos_oficial_configuration', ['name' => 'betatester', 'value' => 0, 'type' => 'analitica']) &&
    Db::getInstance()->insert('correos_oficial_configuration', ['name' => 'Analitica_date', 'value' => date('Y-m-d H:i:s'), 'type' => 'analitica']);
}
