<?php

/**
 * -------------------------------------------------------------------------
 * favorites plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2026 by the favorites plugin team.
 * @license   MIT https://opensource.org/licenses/mit-license.php
 * @link      https://github.com/Sevengroup-IT/favorites
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
     * @return array
     */
    public static function getFavoriteList()
    {
        global $DB;

        $columns = ['id', 'user_id', 'order', 'type'];

        $favorite = new Favorite();
        $favorite->getFromDB($_SESSION['glpiID']);

        $criteria = [
            'SELECT' => $columns,
            'FROM' => 'glpi_plugin_favorites_favorites',
            'WHERE' => ['user_id' => $_SESSION['glpiID']],
            'ORDERBY' => 'order ASC',
        ];
        $iterator = $DB->request($criteria);

        $list = [];
        if (count($iterator) > 0) {
            foreach ($iterator as $data) {
                if (method_exists($data['type'], 'getMenuContent')) {
                    $list[$data['type']] = $data['type']::getMenuContent();
                }
            }
        }

        return $list;
    }

    /**
     * @param $menus
     * @return array|array[]
     */
    public static function redefineMenus($menus)
    {
        if (self::canView()) {
            $collection = self::getFavoriteList();

            $favorites_menu = [PLUGIN_FAVORITES =>
                [
                    'title' => self::getMenuName(),
                    'types' => [array_keys($collection)],
                    'links' => [
                        'search' => self::getSearchURL(false),
                        'lists' => ''
                    ],
                    'icon' => self::getIcon(),
                    'content' => [],
                    'default' => '/front/favorites.php'
                ]
            ];
            if (self::canCreate()) {
                $favorites_menu[PLUGIN_FAVORITES]['links']['add'] = self::getFormURL(false);
            }

            $content = [];
            if (!empty($collection)) {
                foreach ($collection as $key => $val) {
                    $content[strtolower($key)] = $val;
                }
            } else {
                $content['default'] = [
                    'title' => __('Default', PLUGIN_FAVORITES),
                    'icon' => self::getIcon(),
                    'page' => self::getSearchURL(false),
                    'default' => self::getSearchURL(''),
                ];
            }

            $favorites_menu[PLUGIN_FAVORITES]['content'] = $content;

            // return favorites menu always in first
            $menus = $favorites_menu + $menus;
        }

        \Safe\file_put_contents('C:\Users\tbrouard\Sources\repositories\glpi-test\plugins\favorites\menu.json',json_encode($menus));

        return $menus;
    }

    /**
     * @return array
     */
    public static function getDropDown()
    {
        $list = [];

        $menus = $_SESSION['glpimenu'];


        return $list;
    }
}
