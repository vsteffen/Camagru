<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit63325d1418e3782bf3ded9b68703f1f8
{
    public static $prefixLengthsPsr4 = array (
        'v' => 
        array (
            'vsteffen\\' => 9,
        ),
        'A' => 
        array (
            'Abraham\\TwitterOAuth\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'vsteffen\\' => 
        array (
            0 => __DIR__ . '/../..' . '/class',
        ),
        'Abraham\\TwitterOAuth\\' => 
        array (
            0 => __DIR__ . '/..' . '/abraham/twitteroauth/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit63325d1418e3782bf3ded9b68703f1f8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit63325d1418e3782bf3ded9b68703f1f8::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
