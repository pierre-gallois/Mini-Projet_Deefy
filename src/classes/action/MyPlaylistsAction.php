<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

// fonctionnalité 1 : mes playlists
class MyPlaylistsAction extends Action
{
    protected function executeGet(): string
    {
        $playlists = DeefyRepository::getInstance()->findAllPlaylists();

        if (count($playlists) === 0) {
            return "<h2>Mes playlists</h2><p>Aucune playlist pour l'instant.</p>";
        }

        $html = "<h2>Mes playlists</h2><ul>";
        foreach ($playlists as $pl) {
            // fonctionnalité 2 : chaque playlist est cliquable
            $nom = filter_var($pl->nom, FILTER_SANITIZE_SPECIAL_CHARS);
            $html .= "<li><a href=\"?action=playlist&amp;id={$pl->id}\">$nom</a></li>";
        }
        $html .= "</ul>";
        return $html;
    }
}
