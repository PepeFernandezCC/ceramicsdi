<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf7936497110af74983862c7c5323195d
{
    public static $classMap = array (
        'Ps_Currencyselector' => __DIR__ . '/../..' . '/ps_currencyselector.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitf7936497110af74983862c7c5323195d::$classMap;

        }, null, ClassLoader::class);
    }
}
