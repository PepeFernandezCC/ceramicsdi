<?php
// autoload_static.php @generated by Composer

namespace Composer\Autoload;
class ComposerStaticInitf8a18620c061e70a418b2a3f7d6d548f
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Gmerchantcenterpro\\Xml\\' => 23,
            'Gmerchantcenterpro\\Reviews\\' => 27,
            'Gmerchantcenterpro\\ModuleLib\\' => 29,
            'Gmerchantcenterpro\\Models\\' => 26,
            'Gmerchantcenterpro\\Install\\' => 27,
            'Gmerchantcenterpro\\Hook\\' => 24,
            'Gmerchantcenterpro\\Exclusion\\' => 29,
            'Gmerchantcenterpro\\Dao\\' => 23,
            'Gmerchantcenterpro\\Controllers\\' => 31,
            'Gmerchantcenterpro\\Configuration\\' => 33,
            'Gmerchantcenterpro\\Common\\' => 26,
            'Gmerchantcenterpro\\Admin\\' => 25,
        ),
    );
    public static $prefixDirsPsr4 = array (
        'Gmerchantcenterpro\\Xml\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/xml',
        ),
        'Gmerchantcenterpro\\Reviews\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/reviews',
        ),
        'Gmerchantcenterpro\\ModuleLib\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'Gmerchantcenterpro\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/models',
        ),
        'Gmerchantcenterpro\\Install\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/install',
        ),
        'Gmerchantcenterpro\\Hook\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/hook',
        ),
        'Gmerchantcenterpro\\Exclusion\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/exclusion',
        ),
        'Gmerchantcenterpro\\Dao\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/dao',
        ),
        'Gmerchantcenterpro\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/controllers',
        ),
        'Gmerchantcenterpro\\Configuration\\' => 
        array (
            0 => __DIR__ . '/../..' . '/conf',
        ),
        'Gmerchantcenterpro\\Common\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/common',
        ),
        'Gmerchantcenterpro\\Admin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/admin',
        ),
    );
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf8a18620c061e70a418b2a3f7d6d548f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf8a18620c061e70a418b2a3f7d6d548f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf8a18620c061e70a418b2a3f7d6d548f::$classMap;
        }, null, ClassLoader::class);
    }
}
