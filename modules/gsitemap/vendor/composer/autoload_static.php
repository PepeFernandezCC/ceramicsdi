<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc1858457495fe95d40a34de97811936c
{
    public static $classMap = array (
        'Gsitemap' => __DIR__ . '/../..' . '/gsitemap.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitc1858457495fe95d40a34de97811936c::$classMap;

        }, null, ClassLoader::class);
    }
}
