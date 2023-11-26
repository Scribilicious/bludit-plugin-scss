<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb9476b606a70fcec284d6b62f6387b6c
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'ScssPhp\\ScssPhp\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ScssPhp\\ScssPhp\\' => 
        array (
            0 => __DIR__ . '/..' . '/scssphp/scssphp/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb9476b606a70fcec284d6b62f6387b6c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb9476b606a70fcec284d6b62f6387b6c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb9476b606a70fcec284d6b62f6387b6c::$classMap;

        }, null, ClassLoader::class);
    }
}