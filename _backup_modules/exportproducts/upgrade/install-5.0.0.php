<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_5_0_0($object)
{
    return $object->upgradeTo500();
}