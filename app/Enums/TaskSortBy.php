<?php

namespace App\Enums;

enum TaskSortBy: string
{
    case CREATED_AT = 'created_at';
    case COMPLETED_AT = 'completed_at';
    case PRIORITY = 'priority';
}
