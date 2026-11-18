<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\exception\InvalidPropertyNameException;

// liste de pistes audio
class AudioList
{
    protected string $nom;
    protected int $nbPistes;
    protected int $dureeTotale;
    protected array $pistes;

    // id dans la base de données (null tant que la liste n'est pas enregistrée)
    protected ?int $id = null;

    public function __construct(string $nom, array $pistes = [])
    {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nbPistes = count($pistes);
        $this->dureeTotale = $this->calculerDuree();
    }

    protected function calculerDuree(): int
    {
        $total = 0;
        foreach ($this->pistes as $piste) {
            $total += $piste->duree;
        }
        return $total;
    }

    public function __get(string $attrName): mixed
    {
        if (property_exists($this, $attrName)) {
            return $this->$attrName;
        }
        throw new InvalidPropertyNameException("invalid property : $attrName");
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
