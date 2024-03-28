<?php

namespace UX\PersonalArea\Pub\Controller;

use XF\Pub\Controller\AbstractController;

class Themes extends AbstractController
{
    // ROLES IDs //
    public static int $default = 1;
    public static int $white = 2;
    public static int $dark = 3;

    public static function Style($user_style)
    {
        switch ($user_style) {
            case self::$default:
                return 'default';
            case self::$white:
                return 'white';
            case self::$dark:
                return 'black';
        }
        return 'default';
    }

    public static function Background($user_style) {
        switch($user_style) {
            case self::$default:
                return '#3c3c3c';
            case self::$white:
                return '#eee';
            case self::$dark:
                return '#242527';
        }
        return '#3c3c3c';
    }
}