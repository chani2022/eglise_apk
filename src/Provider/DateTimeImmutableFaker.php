<?php

namespace App\Provider;

use Faker\Provider\Base;
use Faker\Provider\DateTime;

class DateTimeImmutableFaker extends Base
{
    public static function immutableDateTime()
    {
        return \DateTimeImmutable::createFromMutable(
            DateTime::dateTime()
        );
    }
}
