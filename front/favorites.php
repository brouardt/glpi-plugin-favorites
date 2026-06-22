<?php

use GlpiPlugin\Favorites\Favorite;

Session::checkLoginUser();

Html::header(
    __('Favorites', PLUGIN_FAVORITES),
    $_SERVER['PHP_SELF'],
    PLUGIN_FAVORITES,
    Favorite::class
);

Session::checkRight('favorite', READ);

Search::show(Favorite::class);

Html::footer();
