<?php

namespace App\Enums;

enum UserRole: string
{
    case Patient = 'patient';
    case Doctor = 'doctor';
    case Admin = 'admin';
}
