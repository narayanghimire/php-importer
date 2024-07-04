<?php

declare(strict_types=1);

namespace App\Collection;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use stdClass;

/**
 * Class TypedCollection
 *
 * Provides an enhanced version of Illuminate\Collection, providing type-safety.
 *
 * Usage: Extend TypedCollection with a specialized collection class, e.g. ChickenCollection.
 *        To make the ChickenCollection only accept objects of type Chicken, redefine the
 *        ALLOWED_TYPE constant to contain that class' FQN:
 *        protected const ALLOWED_TYPE = \My\Namespace\Or\Alias\Chicken::class;
 *
 * @template T
 */
class TypedCollection extends Collection
{
    protected const ALLOWED_TYPE = stdClass::class;

    /**
     * @var array<T>
     */
    protected $items = [];

    /**
     * @inheritDoc
     * @param array<T> $items
     */
    public function __construct(array $items = [])
    {
        $this->assertValidTypes($items);

        parent::__construct($items);
    }

    /**
     * @param array<T> $items
     * @throws InvalidArgumentException
     */
    protected function assertValidTypes(array $items): void
    {
        array_map(
            function ($item) {
                $this->assertValidType($item);
            },
            $items
        );
    }

    protected function assertValidType($item): void
    {
        if (!is_object($item) || !is_a($item, static::ALLOWED_TYPE)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Only objects of type "%s" are allowed in collection.',
                    static::ALLOWED_TYPE
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function push(...$values)
    {
        foreach ($values as $value) {
            $this->assertValidType($value);
            $this->items[] = $value;
        }

        return $this;
    }
}