<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer
{
    public function __construct(AlbumTrack $track)
    {
        parent::__construct($track);
    }

    protected function compact(): string
    {
        $t = $this->track;
        return "<div class=\"piste\">"
            . "<p>{$t->numero} - <strong>{$this->e($t->titre)}</strong> par {$this->e($t->artiste)}"
            . " ({$this->e($t->album)})</p>"
            . $this->audio()
            . "</div>";
    }

    protected function long(): string
    {
        $t = $this->track;
        return "<div class=\"piste\">"
            . "<h3>{$this->e($t->titre)}</h3>"
            . "<p>Artiste : {$this->e($t->artiste)}</p>"
            . "<p>Album : {$this->e($t->album)} ({$t->annee}), piste n°{$t->numero}</p>"
            . "<p>Genre : {$this->e($t->genre)} - Durée : {$t->duree} s</p>"
            . $this->audio()
            . "</div>";
    }
}