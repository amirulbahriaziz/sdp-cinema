<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card = 'card';
    case Bank = 'bank';
    case Crypto = 'crypto';
}
