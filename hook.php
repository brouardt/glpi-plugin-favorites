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

use GlpiPlugin\Favorites\Favorite;
use GlpiPlugin\Favorites\Profile;

/**
 * Plugin install process
 */
function plugin_favorites_install(): bool
{
    global $DB;

    $migration = new Migration(PLUGIN_FAVORITES_VERSION);

    Config::setConfigurationValues(PLUGIN_FAVORITES_CONFIG, ['version' => PLUGIN_FAVORITES_VERSION]);

    Plugin::registerClass(Profile::class, ['addtabon' => Profile::class]);

    $default_charset = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

    $favorite_table = Favorite::getTable();

    if (!$DB->tableExists($favorite_table)) {
        $DB->doQuery("CREATE TABLE IF NOT EXISTS `$favorite_table` (
         `id` INT $default_key_sign NOT NULL AUTO_INCREMENT,
         `user_id` INT $default_key_sign NOT NULL,
         `order` SMALLINT NOT NULL DEFAULT '0',
         `type` VARCHAR(32) NOT NULL,
         PRIMARY KEY (`id`), 
         KEY `user_id` (`user_id`)
         ) ENGINE=InnoDB DEFAULT CHARSET=$default_charset COLLATE=$default_collation ROW_FORMAT=DYNAMIC;");
        // insert example
        $DB->doQuery("INSERT INTO `$favorite_table` (`user_id`, `order`, `type`) 
        VALUES 
        ({$_SESSION['glpiID']}, 1, 'Computer'),
        ({$_SESSION['glpiID']}, 2, 'User'),
        ({$_SESSION['glpiID']}, 3, 'Ticket');");
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

    $favorite_table = Favorite::getTable();
    $DB->doQuery("DROP TABLE IF EXISTS `$favorite_table`;");

    return true;
}
