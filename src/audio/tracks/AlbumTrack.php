<?php

namespace iutnc\deefy\audio\tracks;

// piste d'un album
class AlbumTrack extends AudioTrack
{
    protected string $album;
    protected int $numero;

    protected string $artiste = '';
    protected int $annee = 0;

    public function __construct(string $titre, string $nomFichier, string $album, int $numero)
    {
        parent::__construct($titre, $nomFichier);
        $this->album = $album;
        $this->numero = $numero;
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }
}
