<?php

namespace App\Enums;

enum ProposalAuditEvent: string
{

    case CREATED = 'CREATED';
    case UPDATED_FIELDS = 'UPDATED_FIELDS';
    case STATUS_CHANGED = 'STATUS_CHANGED';
    case DELETED_LOGICAL = 'DELETED_LOGICAL';

}
