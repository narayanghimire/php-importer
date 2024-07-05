<?php

declare(strict_types=1);

namespace App\Reader;

use App\Model\ItemCollection;

interface DataReaderInterface
{
    public function read(string $filePath): ItemCollection;
}