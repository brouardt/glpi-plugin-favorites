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

use Glpi\Plugin\Hooks;
use GlpiPlugin\Favorite\Favorite;

define('PLUGIN_FAVORITES_VERSION', '1.0.0');

// Minimal GLPI version, inclusive
define("PLUGIN_FAVORITES_MIN_GLPI_VERSION", "11.0.0");

// Maximum GLPI version, exclusive
define("PLUGIN_FAVORITES_MAX_GLPI_VERSION", "11.0.99");

/**
 * Init hooks of the plugin.
 * REQUIRED
 */
function plugin_init_favorite(): void
{
    /** @var array<string, array<string, mixed>> $PLUGIN_HOOKS */
    global $PLUGIN_HOOKS;

    $plugin = new Plugin();
    if (
        $plugin->isInstalled('Favorites')
        && $plugin->isActivated('favorites')
    ) {
        Plugin::registerClass('PluginFavoritesProfile', ['addtabon' => 'Profile']);

        // Add specific files to add to the header : javascript or css
        /*$PLUGIN_HOOKS[Hooks::ADD_CSS]['favorite'] = 'favorite.css';
        $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['favorite'] = 'favorite.js';*/

        // Display a menu entry ?
        Plugin::registerClass(Profile::class, ['addtabon' => ['Profile']]);
        if (Favorite::canView()) { // Right set in change_profile hook
            $PLUGIN_HOOKS[Hooks::MENU_TOADD]['favorites'] = ['plugins' => Favorite::class];

            $PLUGIN_HOOKS[Hooks::ASSIGN_TO_TICKET]['favorites'] = false;
            $PLUGIN_HOOKS[Hooks::CHANGE_PROFILE]['favorites'] = [Profile::class, 'initProfile'];
            $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY]['favorites'] = false;
            $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY_ICON]['favorites'] = Favorite::getIcon();
        }
    }

    $menu = Html::generateMenuSession(true);

    /*
        $result = json_encode($menu);

        file_put_contents('D:\Sites\glpi-test\plugins\favorite\menu.txt',$result);

    */
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array{
 *      name: string,
 *      version: string,
 *      author: string,
 *      license: string,
 *      homepage: string,
 *      requirements: array{
 *          glpi: array{
 *              min: string,
 *              max: string,
 *          }
 *      }
 * }
 */
function plugin_version_favorite(): array
{
    return [
        'name' => _n('Favorite', 'Favorites', 2, 'favorites'),
        'version' => PLUGIN_FAVORITES_VERSION,
        'author' => '<a href="mailto:thierry.brouard@free.fr">Thierry Brouard</a>,Thierry Brouard',
        'license' => 'GPLv2+',
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
 * Check pre-requisites before install
 * OPTIONAL
 */
function plugin_favorite_check_prerequisites(): bool
{
    return true;
}

/**
 * Check configuration process
 * OPTIONAL
 *
 * @param bool $verbose Whether to display message on failure. Defaults to false.
 */
function plugin_favorite_check_config(bool $verbose = false): bool
{
    // Your configuration check
    return true;

    if ($verbose) {
        echo __('Installed / not configured', 'favorites');
    }
    return false;
}
