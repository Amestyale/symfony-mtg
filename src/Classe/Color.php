<?php

namespace App\Classe;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Color
{
    static $colors = null;
    private $code;

    public static function getColors()
    {
        if(!Color::$colors) Color::$colors = json_decode(file_get_contents('../assets/data/colors.json'), true);
        return Color::$colors;
    }

    public function __construct($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getSymbol()
    {
        foreach (Color::getColors() as $color) {
            if($color["symbol"] == $this->code) return $color["svg_uri"];
        }
        return false;
    }
}