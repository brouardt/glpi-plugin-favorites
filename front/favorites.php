<?php

use GlpiPlugin\Favorites\Favorite;

Session::checkLoginUser();

Html::header(
    __('Favorites', 'favorites'),
    $_SERVER['PHP_SELF'],
    'favorites',
    Favorite::class
);

Session::checkRight('config', READ);

Search::show(Favorite::class);

Html::footer();
