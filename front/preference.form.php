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

use GlpiPlugin\Favorites\Preference;

if (!isset($_GET['id'])) {
    $_GET['id'] = '';
}
if (!isset($_GET['withtemplate'])) {
    $_GET['withtemplate'] = '';
}

$preference = new Preference();

if (isset($_POST['add'])) {
    $preference->check(-1, CREATE, $_POST);
    $preference->add($_POST);
} else if (isset($_POST['update'])) {
    if ((int)$_POST['id'] !== Session::getLoginUserID()) {
        Session::addMessageAfterRedirect(
            __s('You do not have permission to modify this item.'),
            false,
            WARNING);
    } else {
        $preference->check($_POST['id'], UPDATE);
        $preference->update($_POST);
    }
}
Html::back();
