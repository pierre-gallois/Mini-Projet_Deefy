<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

// fonctionnalité 3 : ajouter un podcast à la playlist courante
class AddPodcastTrackAction extends Action
{
    protected function executeGet(): string
    {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Il faut d'abord choisir ou créer une playlist.</p>";
        }
        $pl = unserialize($_SESSION['playlist']);
        $nom = filter_var($pl->nom, FILTER_SANITIZE_SPECIAL_CHARS);

        // enctype obligatoire pour pouvoir envoyer un fichier
        return <<<HTML
        <h2>Ajouter un podcast à « $nom »</h2>
        <form method="post" action="?action=add-track" enctype="multipart/form-data">
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre" maxlength="100" required>

            <label for="auteur">Auteur</label>
            <input type="text" id="auteur" name="auteur" maxlength="100">

            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" maxlength="30">

            <label for="duree">Durée (en secondes)</label>
            <input type="number" id="duree" name="duree" min="0" value="0" required>

            <label for="date">Date</label>
            <input type="date" id="date" name="date">

            <label for="userfile">Fichier audio (.mp3)</label>
            <input type="file" id="userfile" name="userfile" accept=".mp3,audio/mpeg" required>

            <button type="submit">Ajouter</button>
        </form>
        HTML;
    }

    protected function executePost(): string
    {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Il faut d'abord choisir ou créer une playlist.</p>";
        }
        $pl = unserialize($_SESSION['playlist']);

        // on récupère et on vérifie les données
        $titre = trim($_POST['titre'] ?? '');
        $auteur = trim($_POST['auteur'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $duree = filter_var($_POST['duree'] ?? '', FILTER_VALIDATE_INT);
        $date = $_POST['date'] ?? '';

        if ($titre === '') {
            return "<p>Le titre est obligatoire.</p>" . $this->executeGet();
        }
        if ($duree === false) {
            return "<p>La durée doit être un nombre entier.</p>" . $this->executeGet();
        }
        // la date doit être au format AAAA-MM-JJ (ce que renvoie un input type="date")
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return "<p>Date invalide.</p>" . $this->executeGet();
        }

        // upload du fichier audio
        $fichier = $this->uploadMp3();
        if ($fichier === null) {
            return "<p>Le fichier doit être un .mp3.</p>" . $this->executeGet();
        }

        $piste = new PodcastTrack($titre, $fichier);
        $piste->setAuteur($auteur);
        $piste->setGenre($genre);
        $piste->setDate($date);
        try {
            $piste->setDuree($duree);
        } catch (InvalidPropertyValueException $e) {
            return "<p>La durée ne peut pas être négative.</p>" . $this->executeGet();
        }

        // on enregistre en base puis on met à jour la playlist en session
        $repo = DeefyRepository::getInstance();
        $repo->saveTrack($piste);
        $repo->addTrackToPlaylist($pl->id, $piste->id, $pl->nbPistes + 1);

        $pl->ajouterPiste($piste);
        $_SESSION['playlist'] = serialize($pl);

        $r = new AudioListRenderer($pl);
        return $r->render(Renderer::COMPACT)
            . "<p><a href=\"?action=add-track\">Ajouter encore une piste</a></p>";
    }

    // enregistre le fichier dans audio/ avec un nom aléatoire
    // retourne le chemin du fichier, ou null si le fichier n'est pas bon
    private function uploadMp3(): ?string
    {
        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $f = $_FILES['userfile'];

        // vérifications demandées dans le projet : extension .mp3 et type audio/mpeg
        if (strtolower(substr($f['name'], -4)) !== '.mp3' || $f['type'] !== 'audio/mpeg') {
            return null;
        }

        // on choisit nous-mêmes le nom (aléatoire) et l'extension .mp3 :
        // impossible de se retrouver avec un fichier .php sur le serveur
        $nom = 'audio/' . bin2hex(random_bytes(16)) . '.mp3';
        if (!move_uploaded_file($f['tmp_name'], __DIR__ . '/../../../' . $nom)) {
            return null;
        }
        return $nom;
    }
}
