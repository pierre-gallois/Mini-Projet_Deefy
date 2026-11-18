<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

// fonctionnalité 4 : créer une playlist vide
class AddPlaylistAction extends Action
{
    // GET : on affiche le formulaire
    protected function executeGet(): string
    {
        return <<<HTML
        <h2>Nouvelle playlist</h2>
        <form method="post" action="?action=add-playlist">
            <label for="nom">Nom de la playlist</label>
            <input type="text" id="nom" name="nom" maxlength="100" required>
            <button type="submit">Créer</button>
        </form>
        HTML;
    }

    // POST : on traite le formulaire
    protected function executePost(): string
    {
        $nom = trim($_POST['nom'] ?? '');
        if ($nom === '' || mb_strlen($nom) > 100) {
            return "<p>Le nom doit faire entre 1 et 100 caractères.</p>" . $this->executeGet();
        }

        // on enregistre en base (requête préparée, donc pas d'injection SQL)
        $pl = new Playlist($nom);
        DeefyRepository::getInstance()->saveEmptyPlaylist($pl);

        // elle devient la playlist courante
        $_SESSION['playlist'] = serialize($pl);

        // le nom est filtré à l'affichage par le renderer (contre le XSS)
        $r = new AudioListRenderer($pl);
        return $r->render(Renderer::COMPACT)
            . "<p><a href=\"?action=add-track\">Ajouter une piste</a></p>";
    }
}
