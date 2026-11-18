<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\PlayList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use PDO;

// toutes les requêtes SQL de l'appli sont ici
// singleton : une seule connexion PDO pour tout le script
class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8";
        $this->pdo = new PDO($dsn, $conf['username'], $conf['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    // à appeler une fois dans index.php avec le chemin du fichier .ini
    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("erreur de lecture du fichier de configuration");
        }
        self::$config = $conf;
    }

    public static function getInstance(): DeefyRepository
    {
        if (self::$instance === null) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    // fonctionnalité 1 : toutes les playlists (sans les pistes, juste id + nom)
    public function findAllPlaylists(): array
    {
        $stmt = $this->pdo->query("SELECT id, nom FROM playlist ORDER BY nom");
        $playlists = [];
        foreach ($stmt->fetchAll() as $row) {
            $pl = new PlayList($row['nom']);
            $pl->setId((int) $row['id']);
            $playlists[] = $pl;
        }
        return $playlists;
    }

    // fonctionnalité 2 : une playlist avec toutes ses pistes
    public function findPlaylistById(int $id): ?PlayList
    {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM playlist WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        $stmt = $this->pdo->prepare(
            "SELECT track.* FROM track
             INNER JOIN playlist2track ON track.id = playlist2track.id_track
             WHERE playlist2track.id_pl = ?
             ORDER BY playlist2track.no_piste_dans_liste"
        );
        $stmt->execute([$id]);

        $pistes = [];
        foreach ($stmt->fetchAll() as $t) {
            $pistes[] = $this->creerPiste($t);
        }

        $pl = new PlayList($row['nom'], $pistes);
        $pl->setId((int) $row['id']);
        return $pl;
    }

    // transforme une ligne de la table track en objet AlbumTrack ou PodcastTrack
    private function creerPiste(array $t): AudioTrack
    {
        if ($t['type'] === 'A') {
            $piste = new AlbumTrack($t['titre'], $t['filename'] ?? '', $t['titre_album'] ?? '', (int) $t['numero_album']);
            $piste->setArtiste($t['artiste_album'] ?? '');
            $piste->setAnnee((int) $t['annee_album']);
        } else {
            $piste = new PodcastTrack($t['titre'], $t['filename'] ?? '');
            $piste->setAuteur($t['auteur_podcast'] ?? '');
            $piste->setDate($t['date_posdcast'] ?? '');
        }
        $piste->setGenre($t['genre'] ?? '');
        $piste->setDuree((int) $t['duree']);
        $piste->setId((int) $t['id']);
        return $piste;
    }

    // fonctionnalité 4 : enregistre une playlist vide et lui donne son id
    public function saveEmptyPlaylist(PlayList $pl): PlayList
    {
        $stmt = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (?)");
        $stmt->execute([$pl->nom]);
        $pl->setId((int) $this->pdo->lastInsertId());
        return $pl;
    }

    // fonctionnalité 3 : enregistre une piste (album ou podcast) dans la table track
    // et lui donne son id
    public function saveTrack(AudioTrack $piste): AudioTrack
    {
        if ($piste instanceof AlbumTrack) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album)
                 VALUES (?, ?, ?, ?, 'A', ?, ?, ?, ?)"
            );
            $stmt->execute([$piste->titre, $piste->genre, $piste->duree, $piste->nomFichier,
                $piste->artiste, $piste->album, $piste->annee, $piste->numero]);
        } else {
            $stmt = $this->pdo->prepare(
                "INSERT INTO track (titre, genre, duree, filename, type, auteur_podcast, date_posdcast)
                 VALUES (?, ?, ?, ?, 'P', ?, ?)"
            );
            $date = $piste->date === '' ? null : $piste->date;
            $stmt->execute([$piste->titre, $piste->genre, $piste->duree, $piste->nomFichier, $piste->auteur, $date]);
        }
        $piste->setId((int) $this->pdo->lastInsertId());
        return $piste;
    }


    public function savePodcastTrack(PodcastTrack $piste): PodcastTrack
    {
        $this->saveTrack($piste);
        return $piste;
    }

    // relie une piste à une playlist
    // le numéro de la piste est calculé ici : dernier numéro de la playlist + 1
    public function addTrackToPlaylist(int $idPlaylist, int $idTrack): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 AS num FROM playlist2track WHERE id_pl = ?"
        );
        $stmt->execute([$idPlaylist]);
        $numero = (int) $stmt->fetch()['num'];

        $stmt = $this->pdo->prepare(
            "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (?, ?, ?)"
        );
        $stmt->execute([$idPlaylist, $idTrack, $numero]);
    }
}