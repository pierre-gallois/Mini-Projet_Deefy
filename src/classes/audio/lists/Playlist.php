<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

// une playlist : on peut ajouter / retirer des pistes quand on veut
class Playlist extends AudioList
{
    public function ajouterPiste(AudioTrack $piste): void
    {
        $this->pistes[] = $piste;
        $this->nbPistes = count($this->pistes);
        $this->dureeTotale = $this->calculerDuree();
    }

    public function supprimerPiste(int $indice): void
    {
        if (isset($this->pistes[$indice])) {
            unset($this->pistes[$indice]);
            // on remet les indices à 0, 1, 2...
            $this->pistes = array_values($this->pistes);
            $this->nbPistes = count($this->pistes);
            $this->dureeTotale = $this->calculerDuree();
        }
    }

    public function ajouterListe(array $pistes): void
    {
        // array_unique avec SORT_REGULAR compare les objets avec == donc enlève les doublons
        $this->pistes = array_values(array_unique(array_merge($this->pistes, $pistes), SORT_REGULAR));
        $this->nbPistes = count($this->pistes);
        $this->dureeTotale = $this->calculerDuree();
    }
}
