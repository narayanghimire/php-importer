<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Model\ItemCollection;
use App\Reader\DataReaderInterface;
use App\Repository\DataRepositoryInterface;
use App\Service\DataImportService;
use Tests\BaseTestCase;
use Exception;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Output\OutputInterface;


class DataImportServiceTest extends BaseTestCase
{
    private ObjectProphecy $readerMock;
    private ObjectProphecy $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->readerMock = $this->prophesize(DataReaderInterface::class);
        $this->repositoryMock = $this->prophesize(DataRepositoryInterface::class);
    }
    public function testImportDataSuccess(): void
    {
        $file = 'test_file.xml';
        $itemCollection = new ItemCollection();

        $this->readerMock
            ->read($file)
            ->shouldBeCalled()
            ->willReturn($itemCollection);
        $outputMock = $this->prophesize(OutputInterface::class);
        $this->repositoryMock
            ->save($itemCollection, $outputMock->reveal())
            ->shouldBeCalled();
        $service = new DataImportService(
            $this->readerMock->reveal(),
            $this->repositoryMock->reveal()
        );
        $service->importData($file, $outputMock->reveal()) ;
    }

    public function testImportDataThrowExceptionOnInvalidFile(): void
    {
        $file = 'test_file.xml';
        $this->readerMock
            ->read($file)
            ->shouldBeCalled()
            ->willThrow(
                new Exception('some thing went wrong on reader')
            );
        $outputMock = $this->prophesize(OutputInterface::class);
        $service = new DataImportService(
            $this->readerMock->reveal(),
            $this->repositoryMock->reveal()
        );
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('some thing went wrong on reader');
        $service->importData($file, $outputMock->reveal()) ;
    }

    public function testImportDataThrowExceptionOnSaving(): void
    {
        $file = 'test_file.xml';
        $itemCollection = new ItemCollection();

        $this->readerMock
            ->read($file)
            ->shouldBeCalled()
            ->willReturn($itemCollection);
        $outputMock = $this->prophesize(OutputInterface::class);
        $this->repositoryMock
            ->save($itemCollection, $outputMock->reveal())
            ->willThrow(
                new Exception('some thing went wrong while saving')
            );
        $service = new DataImportService(
            $this->readerMock->reveal(),
            $this->repositoryMock->reveal()
        );
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('some thing went wrong while saving');
        $service->importData($file, $outputMock->reveal()) ;
    }
}