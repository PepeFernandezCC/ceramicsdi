<?php

if (!defined('_PS_VERSION_'))
  exit;

function upgrade_module_5_1_0($object)
{
  return $object->upgradeTo5_1_0();
}