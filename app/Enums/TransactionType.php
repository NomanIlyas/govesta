<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TransactionType extends Enum
{
    const Buy = 1;
    const Rent = 2;
    const Lease = 3;
}
