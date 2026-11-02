<?php

namespace App\CourseAnswers\Entity\Product;

enum Status: string
{
    case SEND = 'send';
    case PAID = 'paid';
    case FAILED = 'failed';
}
