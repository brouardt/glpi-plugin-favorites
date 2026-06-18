<?php

Session::checkLoginUser();

Html::header(
    __('Favorites', 'favorites'),
    $_SERVER['PHP_SELF'],
    'config',
    'pluginFavorites'
);

Session::checkRight('config', READ);

Search::show('PluginFavorites');

Html::footer();
