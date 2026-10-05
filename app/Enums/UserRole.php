<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Shipper = 'shipper';
    case Admin = 'admin';
}
