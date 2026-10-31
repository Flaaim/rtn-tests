<?php

namespace App\CourseAnswers\Entity\File;

enum Status: string
{
    case SEND = 'send';
    case PAID = 'paid';
    case FAILED = 'failed';
}
