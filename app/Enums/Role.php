<?php

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case PETUGAS_KESEHATAN = 'petugas_kesehatan';
}
