<?php

namespace App\Enums;

enum ProposalOrigin: string
{
    case APP = 'APP';
    case SITE = 'SITE';
    case API = 'API';

}
