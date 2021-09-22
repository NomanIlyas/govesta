<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class MarketStatus extends Enum
{
    const Open = 0;
    const Reserved = 1;
    const Sold = 2;
}
