<?php

namespace GlpiPlugin\Favorite;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class Favorit extends \CommonDBTM
{
    public static $rightname = 'plugin_favorite';

    static function getTypeName($nb = 0)
    {
        return _n('Favorit', 'Favorites', $nb, 'favorites');
    }

    public static function getMenuName()
    {
        return __s('Favorite plugin');
    }
    /**
     * @return string
     */
    static function getIcon()
    {
        return "ti ti-heart";
    }

    public static function getAdditionalMenuLinks()
    {
        global $CFG_GLPI;

        $links = [];

        $links['config'] = '/plugins/favorite/index.php';
        $links["<img  src='" . $CFG_GLPI['root_doc'] . "/pics/menu_showall.png' title='" . __s('Show all') . "' alt='" . __s('Show all') . "'>"] = '/plugins/favorite/index.php';
        $links[__s('Test link', 'example')] = '/plugins/favorite/index.php';

        return $links;
    }
}
