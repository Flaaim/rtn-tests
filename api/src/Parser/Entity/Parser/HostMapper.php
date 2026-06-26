<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

enum HostMapper: string
{
    case AUTH = 'Auth/Admin';
    case PATH_QUESTIONS = 'Admin/Info/GetTicketInfo/';
    case PATH_ANSWERS = 'Admin/Archive/QuestionInfo';
}
