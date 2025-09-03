<?php

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_5_0_6($object)
{
    return $object->upgradeTo506();
}