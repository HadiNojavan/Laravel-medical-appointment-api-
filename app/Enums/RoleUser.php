<?php

namespace App\Enums;

enum RoleUser: string
{
    case Patient = 'patient';
    case Doctor = 'doctor';
    case Admin = 'admin';
}
