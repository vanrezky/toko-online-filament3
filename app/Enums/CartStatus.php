<?php

namespace App\Enums;

enum CartStatus: string
{
    case Active = "active";
    case Checked_out = "checked_out";
    case Abandoned = "abandoned";
}
