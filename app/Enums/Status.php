<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Status extends Enum
{
    const Disabled = 0;
    const Enabled = 1;
    const Pending = 2;
    const Draft = 3;
    const Published = 4;
    const Deleted = 5;
    const Confirmed = 6;
}
