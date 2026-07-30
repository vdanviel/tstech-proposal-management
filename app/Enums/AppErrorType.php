<?php

namespace App\Enums;

enum AppErrorType: string
{

    case SYSTEM = "SYSTEM_ERROR";
    case VALIDATION = "VALIDATION_ERROR";
    case NOTFOUND = "NOT_FOUND_ERROR";

}
