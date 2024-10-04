<?php

namespace Candelabro\Enums;

enum CandleColor: string
{
    case WHITE = 'white';
    case GREEN = 'green';
    case ORANGE = 'orange';
    case RED = 'red';
    case PURPLE = 'purple';

    public function getListUrl(): string
    {
        return match ($this) {
            self::WHITE => 'https://padremarcelorossi.com.br/ListaVelaBranca.php',
            self::GREEN => 'https://padremarcelorossi.com.br/ListaVelaVerde.php',
            self::ORANGE => 'https://padremarcelorossi.com.br/ListaVelaLaranja.php',
            self::RED => 'https://padremarcelorossi.com.br/ListaVelaVermelha.php',
            self::PURPLE => 'https://padremarcelorossi.com.br/ListaVelaRoxa.php',
        };
    }

    public function getFormUrl(): string
    {
        return match ($this) {
            self::WHITE => 'https://padremarcelorossi.com.br/gravaDadosVelaBranca.php',
            self::GREEN => 'https://padremarcelorossi.com.br/gravaDadosVelaVerde.php',
            self::ORANGE => 'https://padremarcelorossi.com.br/gravaDadosVelaLaranja.php',
            self::RED => 'https://padremarcelorossi.com.br/gravaDadosVelaVermelha.php',
            self::PURPLE => 'https://padremarcelorossi.com.br/gravaDadosVelaRoxa.php',
        };
    }
}
