<?php

namespace GlpiPlugin\Favorite;

use CommonDBTM;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class Favorite extends CommonDBTM
{
    public static $rightname = 'plugin_favorite';

    public static function getTypeName($nb = 0)
    {
        return _n('Favorite', 'Favorite', $nb, 'Favorite');
    }

    public static function getMenuName()
    {
        return __s('Favorite plugin');
    }

    public static function getMenuContent()
    {
        $title = self::getMenuName(Session::getPluralNumber());
        $search = self::getSearchURL(false);
        $form = self::getFormURL(false);

        // define base menu
        $menu = [
            'title' => __("Favorite", 'favorite'),
            'page' => $search,

            // define sub-options
            // we may have multiple pages under the "Plugin > My type" menu
            'options' => [
                'superasset' => [
                    'title' => $title,
                    'page' => $search,

                    //define standard icons in sub-menu
                    'links' => [
                        'search' => $search,
                        'add' => $form
                    ]
                ]
            ]
        ];

        return $menu;
    }

    /**
     * @return string
     */
    public static function getIcon()
    {
        return "ti ti-heart";
    }
}
