<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_2($module)
{
    if (method_exists($module, 'installAdminTab')) {
        return $module->installAdminTab();
    }

    return true;
}
