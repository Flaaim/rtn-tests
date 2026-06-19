<?php

declare(strict_types=1);

namespace App\Parser\Entity\Parser;

use App\SharedDomain\AggregateRoot;
use App\SharedDomain\Event\EventTrait;

final class Parser implements AggregateRoot
{
    use EventTrait;
    public function __construct(
        private ParserId $id,
        private string $host,
        private Course  $course,
        private Cookie  $cookie
    ) {

    }


}
