<?php

namespace App\Repository;

use App\Model\ItemCollection;
use Symfony\Component\Console\Output\OutputInterface;

interface DataRepositoryInterface
{
    public function save(ItemCollection $itemCollection, OutputInterface $output): void;
}