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

use Glpi\Http;
use Glpi\Plugin\Hooks;

define('PLUGIN_FAVORITES_VERSION', '1.0.0');

// Minimal GLPI version, inclusive
define("PLUGIN_FAVORITES_MIN_GLPI_VERSION", "11.0.0");

// Maximum GLPI version, exclusive
define("PLUGIN_FAVORITES_MAX_GLPI_VERSION", "11.0.99");

/**
 * Init hooks of the plugin.
 * REQUIRED
 */
function plugin_init_favorites(): void
{
    /** @var array<string, array<string, mixed>> $PLUGIN_HOOKS */
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['favorites'] = true;

    $plugin = new Plugin();
    if (
        $plugin->isInstalled('favorites')
        && $plugin->isActivated('favorites')
    ) {
        Plugin::registerClass('PluginFavoritesProfile', ['addtabon' => 'Profile']);

        // Add specific files to add to the header : javascript or css
        $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['favorites'] = 'favorites.js';
        $PLUGIN_HOOKS[Hooks::ADD_CSS]['favorites']        = 'favorites.css';
    }

    $menu = Http::generateMenuSession(true);
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
function plugin_version_favorites(): array
{
    return [
        'name'           => 'FAVORITES',
        'version'        => PLUGIN_FAVORITES_VERSION,
        'author'         => '<a href="mailto:thierry.brouard@free.fr">Thierry Brouard</a>',
        'license'        => 'GPLv2+',
        'homepage'       => '',
        'requirements'   => [
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
function plugin_favorites_check_prerequisites(): bool
{
    return true;
}

/**
 * Check configuration process
 * OPTIONAL
 *
 * @param bool $verbose Whether to display message on failure. Defaults to false.
 */
function plugin_favorites_check_config(bool $verbose = false): bool
{
    // Your configuration check
    return true;

    // Example:
    // if ($verbose) {
    //    echo __('Installed / not configured', '{LNAME}');
    // }
    // return false;
}
