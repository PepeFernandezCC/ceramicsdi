<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit352757c394d1be92dc35b192fcfb51ca
{
    public static $classMap = array (
        'Gsitemap' => __DIR__ . '/../..' . '/gsitemap.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit352757c394d1be92dc35b192fcfb51ca::$classMap;

        }, null, ClassLoader::class);
    }
}
