<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case CANCELED = 'CANCELED';

    public function ableToTransitionStatus(self $to){

        return match ($this) {//objeto que chamou o metodo
            self::DRAFT => in_array($to, [self::SUBMITTED, self::CANCELED]),//se status atual for draft e o status $to for submmited ou canceled, ele aprova
            self::SUBMITTED => in_array($to, [self::APPROVED, self::REJECTED, self::CANCELED]),
            self::REJECTED => in_array($to, [self::SUBMITTED]),
            self::APPROVED => false,
            self::CANCELED => false
        };

    }
}
