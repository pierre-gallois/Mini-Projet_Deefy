<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

// affichage d'une liste (album ou playlist)
class AudioListRenderer implements Renderer
{
    private AudioList $liste;

    public function __construct(AudioList $liste)
    {
        $this->liste = $liste;
    }

    public function render(int $selector = Renderer::COMPACT): string
    {
        $html = "<h2>" . filter_var($this->liste->nom, FILTER_SANITIZE_SPECIAL_CHARS) . "</h2>";

        if ($this->liste->nbPistes === 0) {
            $html .= "<p>Cette playlist est vide.</p>";
        }

        foreach ($this->liste->pistes as $piste) {
            // on choisit le bon renderer selon le type de piste
            if ($piste instanceof AlbumTrack) {
                $r = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof PodcastTrack) {
                $r = new PodcastRenderer($piste);
            } else {
                continue;
            }
            $html .= $r->render(Renderer::COMPACT);
        }

        $html .= "<p>{$this->liste->nbPistes} piste(s) - durée totale : {$this->liste->dureeTotale} s</p>";
        return $html;
    }
}