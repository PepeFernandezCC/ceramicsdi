<?php
/**
 * Copyright 2025 LÍNEA GRÁFICA E.C.E S.L.
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
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_0($module)
{
    $context = Context::getContext();

    $id_shop = $context->shop->id;
    $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

    $result = true;

    $configurations_list = [
        'LGSEO_AUTOMATIC_REDIRECTIONS' => [
            'default_value' => false,
            'auto_proccess' => true,
            'add_field_value' => true,
            'html' => false,
        ],
    ];

    foreach (Shop::getShops(false, null, true) as $id_shop) {
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        foreach ($configurations_list as $configuration_name => $configuration) {
            $result &= Configuration::updateValue(
                $configuration_name,
                $configuration['default_value'],
                $configuration['html'],
                (int) $id_shop_group,
                (int) $id_shop
            );
        }
    }

    $result &= $module->registerHook('actionDispatcherBefore');

    return $result;
}
