<?php

namespace App\Enums;

enum CustomerTypeEnum:string
{
    case PERSON = 'person';
    case COMPANY = 'company';
}