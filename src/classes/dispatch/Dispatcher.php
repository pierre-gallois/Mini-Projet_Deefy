<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\DefaultAction;

// choisit l'action à exécuter selon le paramètre ?action=... puis affiche la page
class Dispatcher
{
    private string $action;

    // tableau 'valeur du paramètre action' => 'nom de la classe Action'
    private array $routes = [];

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $class): self
    {
        $this->routes[$action] = $class;
        return $this;
    }

    public function run(): void
    {
        // si l'action n'existe pas, on affiche l'accueil
        $class = $this->routes[$this->action] ?? DefaultAction::class;
        $act = new $class();

        try {
            // grâce à __invoke(), $act() lance execute()
            $html = $act();
        } catch (\PDOException $e) {
            // on n'affiche pas le message de PDO (il peut contenir des infos sur la base)
            $html = "<p>Erreur avec la base de données.</p>";
        }

        $this->renderPage($html);
    }

    // le seul echo de tout le projet
    private function renderPage(string $html): void
    {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Deefy</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
        </head>
        <body>
            <header class="container">
                <h1>Deefy</h1>
                <nav>
                    <ul>
                        <li><a href="?action=default">Accueil</a></li>
                        <li><a href="?action=my-playlists">Mes playlists</a></li>
                        <li><a href="?action=add-playlist">Créer une playlist</a></li>
                        <li><a href="?action=playlist">PlayList courante</a></li>
                    </ul>
                </nav>
            </header>
            <main class="container">
                $html
            </main>
        </body>
        </html>
        HTML;
    }
}
