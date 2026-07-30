<?php

namespace App\Enums;

enum AppErrorType: string
{

    case SYSTEM = "SYSTEM_ERROR";
    case VALIDATION = "VALIDATION_ERROR";
    case NOTFOUND = "NOT_FOUND_ERROR";
    case INVALID_TRANSITION = "INVALID_TRANSITION";
    case AUTH = "AUTH_ERROR";

}
