<?php

namespace iutnc\deefy\audio\tracks;

// un podcast
class PodcastTrack extends AudioTrack
{
    protected string $auteur = '';
    protected string $date = '';

    public function __construct(string $titre, string $nomFichier)
    {
        parent::__construct($titre, $nomFichier);
    }

    public function setAuteur(string $auteur): void
    {
        $this->auteur = $auteur;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }
}
