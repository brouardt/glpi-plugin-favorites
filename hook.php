<?php

/**
 * -------------------------------------------------------------------------
 * Favorite plugin for GLPI
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
