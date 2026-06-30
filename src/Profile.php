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

use CommonGLPI;
use DbUtils;
use Glpi\Application\View\TemplateRenderer;
use ProfileRight;
use Session;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class Profile extends \Profile
{
    public static $rightname = PLUGIN_FAVORITES_RIGHTS;

    /**
     * @param CommonGLPI $item
     * @param int $withtemplate
     *
     * @return string
     */
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item->getType() == 'Profile') {
            return self::createTabEntry(__s('Favorites', PLUGIN_FAVORITES));
        }

        return '';
    }

    /**
     * @return string
     */
    public static function getIcon()
    {
        return 'ti ti-heart';
    }

    /**
     * @param CommonGLPI $item
     * @param int $tabnum
     * @param int $withtemplate
     *
     * @return bool
     */
    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if (!$item instanceof \Profile || !self::canView()) {
            return false;
        }

        $profile = new \Profile();
        $profile->getFromDB($item->getID());

        $rights = self::getAllRights();

        $twig = TemplateRenderer::getInstance();
        $twig->display('@favorites/profile.html.twig', [
            'id' => $item->getID(),
            'profile' => $profile,
            'title' => self::getTypeName(Session::getPluralNumber()),
            'rights' => $rights,
        ]);

        return true;
    }

    /**
     * @return array[]
     */
    static function getAllRights()
    {
        return [
            [
                'itemtype' => Preference::class,
                'label' => __s('Favorites', PLUGIN_FAVORITES),
                'field' => PLUGIN_FAVORITES_RIGHTS,
                'rights' => \Profile::getRightsFor(Preference::class)
            ]
        ];
    }

    /**
     * @param $profile_id
     */
    public static function createFirstAccess($profile_id)
    {
        self::addDefaultProfileInfos($profile_id, [PLUGIN_FAVORITES_RIGHTS => ALLSTANDARDRIGHT], true);
    }

    /**
     * @param $profiles_id
     * @param $rights
     * @return void
     */
    public static function addDefaultProfileInfos($profiles_id, $rights, $drop_existing = false)
    {
        $profileRight = new ProfileRight();

        $dbu = new DbUtils();
        foreach ($rights as $right => $value) {
            if ($dbu->countElementsInTable(
                    'glpi_profilerights',
                    ['profiles_id' => $profiles_id, 'name' => $right]
                ) && $drop_existing) {
                $profileRight->deleteByCriteria(['profiles_id' => $profiles_id, 'name' => $right]);
            }
            if (!$dbu->countElementsInTable(
                'glpi_profilerights',
                [
                    'profiles_id' => $profiles_id,
                    'name' => $right
                ]
            )) {
                $myright['profiles_id'] = $profiles_id;
                $myright['name'] = $right;
                $myright['rights'] = $value;
                $profileRight->add($myright);

                //Add right to the current session
                $_SESSION['glpiactiveprofile'][$right] = $value;
            }
        }
    }

    /**
     * Initialize profiles
     */
    public static function initProfile()
    {
        global $DB;

        $profile = new self();
        $dbu = new DbUtils();

        //Add new rights in glpi_profilerights table
        foreach ($profile->getAllRights() as $data) {
            if ($dbu->countElementsInTable(
                    'glpi_profilerights',
                    ['name' => $data['field']]
                ) == 0) {
                ProfileRight::addProfileRights([$data['field']]);
            }
        }

        $it = $DB->request([
            'FROM' => 'glpi_profilerights',
            'WHERE' => [
                'profiles_id' => $_SESSION['glpiactiveprofile']['id'],
                'name' => ['LIKE', '%' . PLUGIN_FAVORITES_RIGHTS . '%'],
            ],
        ]);
        foreach ($it as $prof) {
            if (isset($_SESSION['glpiactiveprofile'])) {
                $_SESSION['glpiactiveprofile'][$prof['name']] = $prof['rights'];
            }
        }
    }

    /**
     * @return void
     */
    public static function removeRightsFromSession()
    {
        foreach (self::getAllRights() as $right) {
            if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
                unset($_SESSION['glpiactiveprofile'][$right['field']]);
            }
        }
    }
}
