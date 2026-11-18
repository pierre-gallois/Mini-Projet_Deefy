<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastRenderer extends AudioTrackRenderer
{
    public function __construct(PodcastTrack $track)
    {
        parent::__construct($track);
    }

    protected function compact(): string
    {
        $t = $this->track;
        return "<div class=\"piste\">"
            . "<p><strong>{$this->e($t->titre)}</strong> par {$this->e($t->auteur)} (podcast)</p>"
            . $this->audio()
            . "</div>";
    }

    protected function long(): string
    {
        $t = $this->track;
        return "<div class=\"piste\">"
            . "<h3>{$this->e($t->titre)}</h3>"
            . "<p>Auteur : {$this->e($t->auteur)} - Date : {$this->e($t->date)}</p>"
            . "<p>Genre : {$this->e($t->genre)} - Durée : {$t->duree} s</p>"
            . $this->audio()
            . "</div>";
    }
}