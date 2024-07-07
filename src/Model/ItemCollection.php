<?php

declare(strict_types=1);

namespace App\Model;

use App\Collection\TypedCollection;

/**
 * @extends TypedCollection<Item>
 */
class ItemCollection extends TypedCollection
{
    public const string ALLOWED_TYPE = Item::class;
}