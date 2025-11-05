<?php

namespace App\Provider;


class LangueProviderFaker
{
    /**
     * @return string
     */
    public function randomTypeLangue(): string
    {
        $langue_type = ["Fr", "Mg", "En"];
        return $langue_type[random_int(0, count($langue_type) - 1)];
        // return $this->faker->randomElement($langue_type);
    }
}
