<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5893ad8b052a3f7a17b25d8cc9a76ace
{
    public static $classMap = array (
        'Ps_Customeraccountlinks' => __DIR__ . '/../..' . '/ps_customeraccountlinks.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit5893ad8b052a3f7a17b25d8cc9a76ace::$classMap;

        }, null, ClassLoader::class);
    }
}
