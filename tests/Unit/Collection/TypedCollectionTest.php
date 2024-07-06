<?php

declare(strict_types=1);

namespace Tests\Unit\Collection;

use App\Collection\TypedCollection;
use InvalidArgumentException;
use stdClass;
use Tests\BaseTestCase;


class TypedCollectionTest extends BaseTestCase
{
    private function collectionFactory($items = null): TypedCollection
    {
        return new class ($items) extends TypedCollection {
            // no content, just a dummy to implement abstract class
        };
    }
    public function testConstructValidItems(): void
    {
        $items = [new stdClass(), new stdClass(), new stdClass()];
        $collection = $this->collectionFactory($items);

        $this->assertCount(3, $collection);
    }

    public function testConstructInvalidItems(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $items = [new stdClass(), new stdClass(), 'invalid'];
        $this->collectionFactory($items);
    }

    public function testPushMultipleValidItems(): void
    {
        $person       = new stdClass();
        $person->name = "John Doe";

        $personNew       = new stdClass();
        $personNew->name = "John Doe Prepend";
        $collection  = $this->collectionFactory([]);
        $collection->push($person, $personNew);
        $this->assertCount(2, $collection);
    }
}