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

use Glpi\Plugin\Hooks;
use GlpiPlugin\Favorites\Favorite;
use GlpiPlugin\Favorites\Profile;
use GlpiPlugin\Favorites\Config;

define('PLUGIN_FAVORITES', 'favorites');
define('PLUGIN_FAVORITES_CONFIG', 'plugin:favorites');
define('PLUGIN_FAVORITES_RIGHTS', 'plugin_favorites');
define('PLUGIN_FAVORITES_VERSION', '1.0.0');
define('PLUGIN_FAVORITES_MIN_GLPI_VERSION', '11.0.0');
define('PLUGIN_FAVORITES_MAX_GLPI_VERSION', '11.0.99');

/**
 * @return void
 */
function plugin_init_favorites(): void
{
    /** @var array<string, array<string, mixed>> $PLUGIN_HOOKS */
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS[Hooks::ASSIGN_TO_TICKET][PLUGIN_FAVORITES] = false;
    $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY][PLUGIN_FAVORITES] = false;
    $PLUGIN_HOOKS[Hooks::CHANGE_PROFILE][PLUGIN_FAVORITES] = [Profile::class, 'initProfile'];

    Plugin::registerClass(Profile::class, ['addtabon' => 'Profile']);

    $plugin = new Plugin();
    if (Session::getLoginUserID() && $plugin->isActivated(PLUGIN_FAVORITES)) {

        if (Session::haveRight(PLUGIN_FAVORITES_RIGHTS, READ)) {
            $PLUGIN_HOOKS[Hooks::REDEFINE_MENUS][PLUGIN_FAVORITES] = [Favorite::class, 'redefineMenus'];
            $PLUGIN_HOOKS[Hooks::AUTO_GET_DROPDOWN][PLUGIN_FAVORITES] = [Favorite::class, 'getDropdown'];
        }

        // Display a config entry
        $PLUGIN_HOOKS[Hooks::CONFIG_PAGE][PLUGIN_FAVORITES] = 'front/favorite.php';
    }
}


/**
 * @return array
 */
function plugin_version_favorites(): array
{
    return [
        'name' => __s('Favorites', PLUGIN_FAVORITES),
        'version' => PLUGIN_FAVORITES_VERSION,
        'author' => 'Thierry Brouard',
        'license' => 'GPLv3',
        'homepage' => 'https://github.com/brouardt/glpi-plugin-favorite',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_FAVORITES_MIN_GLPI_VERSION,
                'max' => PLUGIN_FAVORITES_MAX_GLPI_VERSION,
            ],
        ],
    ];
}

/**
 * @return bool
 */
function plugin_favorites_check_prerequisites(): bool
{
    return true;
}

/**
 * @param bool $verbose
 * @return bool
 */
function plugin_favorite_check_config(bool $verbose = false): bool
{
    // Your configuration check
    return true;

    if ($verbose) {
        echo __('Installed / not configured', PLUGIN_FAVORITES);
    }
    return false;
}
