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

use GlpiPlugin\Favorites\Profile;
use GlpiPlugin\Favorites\Preference;

/**
 * Plugin install process
 */
function plugin_favorites_install(): bool
{
    global $DB;

    $migration = new Migration(PLUGIN_FAVORITES_VERSION);

    Config::setConfigurationValues(PLUGIN_FAVORITES_CONFIG, ['version' => PLUGIN_FAVORITES_VERSION]);

    $default_charset = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

    $preference_table = Preference::getTable();
    if (!$DB->tableExists($preference_table)) {
        $DB->doQuery("CREATE TABLE IF NOT EXISTS `$preference_table` (
         `users_id` INT $default_key_sign NOT NULL,
         `types` TEXT, 
         PRIMARY KEY `users_id`
         ) ENGINE=InnoDB DEFAULT CHARSET=$default_charset COLLATE=$default_collation ROW_FORMAT=DYNAMIC;");
    }
    //execute the whole migration
    $migration->executeMigration();

    Profile::initProfile();
    Profile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);

    return true;
}

/**
 * Plugin uninstall process
 */
function plugin_favorites_uninstall(): bool
{
    /** @var DBmysql $DB */
    global $DB;

    $config = new Config();
    $my_config = array_keys(Config::getConfigurationValues(PLUGIN_FAVORITES_CONFIG));
    $config->deleteConfigurationValues(PLUGIN_FAVORITES_CONFIG, $my_config);

    foreach (Profile::getAllRights() as $right) {
        ProfileRight::deleteProfileRights([$right['field']]);
    }

    $preference_table = Preference::getTable();
    $DB->doQuery("DROP TABLE IF EXISTS `$preference_table`;");

    return true;
}
