<?php

// point d'entrée unique de l'application

// l'autoloader doit être chargé AVANT session_start()
// sinon PHP ne connaît pas les classes des objets stockés en session
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\MyPlaylistsAction;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

session_start();

DeefyRepository::setConfig(DIR . '/../config/deefy.db.ini');
$dispatcher = new Dispatcher();
$dispatcher->addRoute('default', DefaultAction::class)
    ->addRoute('my-playlists', MyPlaylistsAction::class)
    ->addRoute('playlist', DisplayPlaylistAction::class)
    ->addRoute('add-playlist', AddPlaylistAction::class)
    ->addRoute('add-track', AddPodcastTrackAction::class);
$dispatcher->run();
