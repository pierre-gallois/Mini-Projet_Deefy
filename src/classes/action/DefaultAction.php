<?php

namespace iutnc\deefy\action;

// page d'accueil (TD12 partie 1)
class DefaultAction extends Action
{
    protected function executeGet(): string
    {
        return "<h2>Bienvenue !</h2>"
            . "<p>Choisissez une action dans le menu.</p>";
    }
}
