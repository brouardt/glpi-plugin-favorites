<?php

namespace GlpiPlugin\Favorites;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class Favorite extends \CommonDBTM
{
    public static $rightname = 'plugin_favorites';

    static function getTypeName($nb = 0)
    {
        return _n('Favorite', 'Favorites', $nb, 'favorites');
    }

    /**
     * @return string
     */
    static function getIcon()
    {
        return "ti ti-heart";
    }
}
