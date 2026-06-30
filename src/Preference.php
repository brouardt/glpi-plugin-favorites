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
use CommonGLPI;
use Glpi\Application\View\TemplateRenderer;
use Session;
use Toolbox;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class Preference extends CommonDBTM
{
    public static $rightname = PLUGIN_FAVORITES_RIGHTS;

    public $can_be_translated = true;

    /**
     * @param $nb
     * @return string
     */
    public static function getTypeName($nb = 0)
    {
        return __s('Favorites', PLUGIN_FAVORITES);
    }

    /**
     * @return string
     */
    public static function getIcon()
    {
        return 'ti ti-heart';
    }

    /**
     * @return void
     */
    public static function showPreferences()
    {
        $preference = Preference::getPreferences();

        if ($preference) {
            $preference['types'] = json_decode($preference['types']);
            $mode = 'update';
        } else {
            $preference['id'] = Session::getLoginUserID();
            $preference['types'] = [];
            $mode = 'add';
        }

        if (self::canCreate() || self::canEdit()) {
            $can_edit = true;
        } else {
            $can_edit = false;
        }

        // compose multiple choice menu
        $menu = $_SESSION['glpimenu'];
        $list = [];
        foreach ($menu as $item => $object) {
            if (!empty($object['types'])) {
                foreach ($object['types'] as $type) {
                    if (!empty($type)
                        && isset($object['content'][strtolower($type)]['title'])
                        && !empty($object['content'][strtolower($type)]['title'])
                    ) {
                        $list[$object['title']][$type] = $object['content'][strtolower($type)]['title'];
                    }
                }
            }
        }

        TemplateRenderer::getInstance()->display('@favorites/preference.html.twig', [
            'action' => Toolbox::getItemTypeFormURL(self::class),
            'preference' => $preference,
            'menus' => $list,
            'mode' => $mode,
            'canedit' => $can_edit
        ]);
    }


    /**
     * @param CommonGLPI $item
     * @param $withtemplate
     * @return string|string[]
     */
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if (($item->getType() == 'Preference')) {
            return self::createTabEntry(self::getTypeName(), 0, $item::getType(), self::getIcon());
        }
        return '';
    }

    /**
     * @param CommonGLPI $item
     * @param $tabnum
     * @param $withtemplate
     * @return true
     */
    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item->getType() == 'Preference') {
            self::showPreferences();
        }

        return true;
    }

    /**
     * @return array
     */
    public static function getPreferences()
    {
        global $DB;

        return $DB->request([
            'SELECT' => ['id', 'types'],
            'FROM' => 'glpi_plugin_favorites_preferences',
            'WHERE' => ['id' => Session::getLoginUserID()],
        ])->current();
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function prepareInput($input)
    {
        if (isset($input['types'])) {
            if (empty($input['types'])) {
                $input['types'] = json_encode([]);
            } else {
                $input['types'] = json_encode($input['types']);
            }
        }

        return $input;
    }

    /**
     * @param $input
     * @return false|mixed[]
     */
    public function prepareInputForAdd($input)
    {
        return $this->prepareInput($input);
    }

    /**
     * @param $input
     * @return false|mixed[]
     */
    public function prepareInputForUpdate($input)
    {
        return $this->prepareInput($input);
    }

}
