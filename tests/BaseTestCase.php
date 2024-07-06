<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BaseTestCase extends TestCase
{
    use ProphecyTrait;
}