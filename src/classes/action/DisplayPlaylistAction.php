<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

// ?action=playlist          -> fonctionnalité 5 : affiche la playlist courante (en session)
// ?action=playlist&id=3     -> fonctionnalité 2 : charge la playlist 3 de la base, elle devient la playlist courante
class DisplayPlaylistAction extends Action
{
    protected function executeGet(): string
    {
        if (isset($_GET['id'])) {
            // on vérifie que l'id est bien un entier
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($id === false) {
                return "<p>Identifiant de playlist invalide.</p>";
            }

            $pl = DeefyRepository::getInstance()->findPlaylistById($id);
            if ($pl === null) {
                return "<p>Cette playlist n'existe pas.</p>";
            }

            // elle devient la playlist courante
            $_SESSION['playlist'] = serialize($pl);
        }

        if (!isset($_SESSION['playlist'])) {
            return "<p>Pas de playlist courante. Choisissez-en une dans <a href=\"?action=my-playlists\">Mes playlists</a>"
                . " ou <a href=\"?action=add-playlist\">créez-en une</a>.</p>";
        }

        $pl = unserialize($_SESSION['playlist']);
        $r = new AudioListRenderer($pl);
        return $r->render(Renderer::COMPACT)
            . "<p><a href=\"?action=add-track\">Ajouter une piste</a></p>";
    }
}
