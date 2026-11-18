<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

// classe abstraite commune aux renderers de pistes
abstract class AudioTrackRenderer implements Renderer
{
    protected AudioTrack $track;

    public function __construct(AudioTrack $track)
    {
        $this->track = $track;
    }

    public function render(int $selector): string
    {
        switch ($selector) {
            case Renderer::LONG:
                return $this->long();
            case Renderer::COMPACT:
            default:
                return $this->compact();
        }
    }

    abstract protected function compact(): string;

    abstract protected function long(): string;

    // balise audio, commune à toutes les pistes
    protected function audio(): string
    {
        // en base on stocke juste le nom du fichier, les mp3 sont dans le dossier audio/
        $fichier = 'audio/' . $this->e($this->track->nomFichier);
        return "<audio controls src=\"$fichier\"></audio>";
    }

    // petite fonction pour filtrer le texte avant de l'afficher (contre le XSS)
    protected function e(string $texte): string
    {
        return filter_var($texte, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}