<?php

namespace iutnc\deefy\action;

// classe mère de toutes les actions (fichier fourni au TD11, complété avec l'exercice 3)
abstract class Action
{
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct()
    {
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    // TD11 ex3 : une méthode par type de requête HTTP
    public function execute(): string
    {
        if ($this->http_method === 'POST') {
            return $this->executePost();
        }
        return $this->executeGet();
    }

    abstract protected function executeGet(): string;

    // par défaut un POST fait la même chose qu'un GET
    protected function executePost(): string
    {
        return $this->executeGet();
    }

    // TD11 ex3 : on peut appeler l'objet comme une fonction : $html = $action();
    public function __invoke(): string
    {
        return $this->execute();
    }
}
