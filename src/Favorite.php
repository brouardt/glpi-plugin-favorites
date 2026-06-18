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
    public static $rightname = 'favorite';

    public static function getTypeName($nb = 0)
    {
        return _n('Favorite', 'Favorites', $nb, 'favorites');
    }

    public static function getMenuName()
    {
        return __s('Favorites', 'favorites');
    }

    public static function getMenuContent()
    {
        $menu = [];
        $menu['title'] = self::getMenuName();
        $menu['page'] = '/';
        $menu['links'] = [
            'search' => '/',
            'lists' => ''
        ];
        $menu['icon'] = self::getIcon();

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
