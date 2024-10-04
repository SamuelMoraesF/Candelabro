<?php

namespace Candelabro\ValueObject;

use Candelabro\Enums\CandleColor;

readonly class Pray
{
    public function __construct(
        public string $name,
        public string $date,
        public string $city,
        public int $days,
        public CandleColor $color,
    ) {
        //
    }
}
