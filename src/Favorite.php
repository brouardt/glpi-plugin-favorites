<?php

/**
 * -------------------------------------------------------------------------
 * favorites plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * GPLv3 License
 *
 * Copyright (C) 2026  Thierry Brouard
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2026 by the favorites plugin team.
 * @license   GPL-3.0 https://opensource.org/license/gpl-3.0
 * @link      https://github.com/brouardt/glpi-plugin-favorite
 * -------------------------------------------------------------------------
 */

namespace GlpiPlugin\Favorites;

use CommonDBTM;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class Favorite extends CommonDBTM
{
    public static $rightname = PLUGIN_FAVORITES_RIGHTS;

    /**
     * @param $nb
     * @return string
     */
    public static function getTypeName($nb = 0)
    {
        return _n('Favorite', 'Favorites', $nb, PLUGIN_FAVORITES);
    }

    /**
     * @return string
     */
    public static function getMenuName()
    {
        return __s('Favorites', PLUGIN_FAVORITES);
    }

    /**
     * @return string
     */
    public static function getIcon()
    {
        return "ti ti-heart";
    }


    /**
     * @param $menus
     * @return array|array[]
     */
    public static function redefineMenus($menus)
    {
        if (self::canView()) {
            $preference = Preference::getPreferences();

            if (isset($preference['types'])) {

                $types = json_decode($preference['types']);

                $favorites_menu = [PLUGIN_FAVORITES =>
                    [
                        'title' => self::getMenuName(),
                        'types' => $types,
                        'icon' => self::getIcon(),
                        'content' => [],
                        'display' => true
                    ]
                ];

                if (!empty($types)) {
                    foreach ($types as $type) {
                        if(!is_string($type)
                            || !class_exists($type)
                            || !is_a($type, CommonDBTM::class, true)
                            || !method_exists($type, 'getMenuContent')
                        ) {
                            continue;
                        }
                        $data = $type::getMenuContent();
                        if (isset($data['is_multi_entries']) && $data['is_multi_entries']) {
                            $favorites_menu[PLUGIN_FAVORITES]['content'] += $data;
                        } else {
                            $favorites_menu[PLUGIN_FAVORITES]['content'][strtolower($type)] = $data;
                        }
                    }
                }

                // return favorites menu always in first
                $menus = $favorites_menu + $menus;
            }
        }

        return $menus;
    }
}
