<?php

namespace App\Enums;


enum StatusType: int
{

    case ACTIVE = 1;
    case INACTIVE = 0;
    case FEATURED = 1;
}
