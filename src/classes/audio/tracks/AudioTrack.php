<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

// classe mère des pistes audio
class AudioTrack
{
    // propriétés initialisées par le constructeur
    protected string $titre;
    protected string $nomFichier;

    protected string $genre = '';
    protected int $duree = 0;

    // clé primaire dans la table track (null tant que la piste n'est pas enregistrée)
    protected ?int $id = null;

    public function __construct(string $titre, string $nomFichier)
    {
        $this->titre = $titre;
        $this->nomFichier = $nomFichier;
    }

    // getter magique
    public function __get(string $attrName): mixed
    {
        if (property_exists($this, $attrName)) {
            return $this->$attrName;
        }
        throw new InvalidPropertyNameException("invalid property : $attrName");
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setDuree(int $duree): void
    {
        if ($duree < 0) {
            throw new InvalidPropertyValueException("invalid value for duree : $duree");
        }
        $this->duree = $duree;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
    }
}
