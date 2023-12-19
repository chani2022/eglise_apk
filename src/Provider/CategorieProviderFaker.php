<?php

namespace App\Provider;


class CategorieProviderFaker
{
    /**
     * @return string
     */
    public function randomTypeCategory(): string
    {
        $categorie_type = ["Populaire", "Récent", "Calendrier d'évènement"];

        return $categorie_type[random_int(0, count($categorie_type) - 1)];
    }
}
