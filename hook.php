<?php

/**
 * -------------------------------------------------------------------------
 * {NAME} plugin for GLPI
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
 * @copyright Copyright (C) {YEAR} by the {NAME} plugin team.
 * @license   MIT https://opensource.org/licenses/mit-license.php
 * @link      https://github.com/pluginsGLPI/{LNAME}
 * -------------------------------------------------------------------------
 */

use GlpiPlugin\Favorite\Favorite;

/**
 * Plugin install process
 */
function plugin_favorite_install(): bool
{
    global $DB;

    $default_charset   = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign  = DBConnection::getDefaultPrimaryKeySignOption();

    $favorite_table = 'glpi_plugin_favorite';

    if (!$DB->tableExists($favorite_table)) {
        $DB->doQuery("
         CREATE TABLE IF NOT EXISTS `$favorite_table` (
         `id`         INT {$default_key_sign} NOT NULL AUTO_INCREMENT,
         `users_id`   INT {$default_key_sign} NOT NULL,
         `menu_order` SMALLINT NOT NULL DEFAULT '0',
         `menu_url`   VARCHAR(255) NOT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;
      ");
    }
    $classes = ['PluginFavoritesFavorite' => Favorite::class];

    return true;
}

/**
 * Plugin uninstall process
 */
function plugin_favorite_uninstall(): bool
{
    /** @var DBmysql $DB */
    global $DB;

    $config = new Config();
    $my_config = array_keys(Config::getConfigurationValues('plugin:Favorite'));
    $config->deleteConfigurationValues('plugin:Favorite', $my_config);

    $favorite_table = 'glpi_plugin_favorite';

    $DB->doQuery("DROP TABLE IF EXISTS `$favorite_table`;");
    ProfileRight::deleteProfileRights([Favorite::$rightname]);

    return true;
}
