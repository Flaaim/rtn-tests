<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Recipient;

enum Status: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
