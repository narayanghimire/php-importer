<?php

namespace Tests\Integration;

use App\config\ContainerConfig;
use App\Repository\DataRepositoryInterface;
use Illuminate\Container\Container;
use Symfony\Component\Process\Process;
use Tests\BaseTestCase;

class ImportDataCommandTest extends BaseTestCase
{
    public function testImportDataCommand(): void
    {
        $entityId = 341;
        $xmlContent = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<catalog>
  <item>
    <entity_id>$entityId</entity_id>
    <categoryName><![CDATA[Green Mountain Ground Coffee]]></categoryName>
    <sku>20</sku>
    <name><![CDATA[Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag]]></name>
    <description/>
    <shortdesc><![CDATA[Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag steeps cup after cup of smoky-sweet, complex dark roast coffee from Green Mountain Ground Coffee.]]></shortdesc>
    <price>41.6000</price>
    <link>http://www.coffeeforless.com/green-mountain-coffee-french-roast-ground-coffee-24-2-2oz-bag.html</link>
    <image>http://mcdn.coffeeforless.com/media/catalog/product/images/uploads/intro/frac_box.jpg</image>
    <Brand><![CDATA[Green Mountain Coffee]]></Brand>
    <Rating>0</Rating>
    <CaffeineType>Caffeinated</CaffeineType>
    <Count>24</Count>
    <Flavored>No</Flavored>
    <Seasonal>No</Seasonal>
    <Instock>Yes</Instock>
    <Facebook>1</Facebook>
    <IsKCup>0</IsKCup>
  </item>
</catalog>
XML;
        $fileName = "test.xml";
        $xmlFilePath = getcwd() . '/resources/'.$fileName;
        file_put_contents($xmlFilePath, $xmlContent);

        $_ENV["XML_FILE_PATH"] = $xmlFilePath;
        $process = new Process(['php', 'bin/console.php', 'data:import']);
        $process->run();
        echo "Command line: " . $process->getCommandLine() . PHP_EOL;
        echo "Output: " . $process->getOutput() . PHP_EOL;
        echo "Error output: " . $process->getErrorOutput() . PHP_EOL;

       $this->assertEquals(0, $process->getExitCode(), 'Command execution failed.');
        $this->assertStringContainsString('Data import successful.', $process->getOutput());

        unlink($xmlFilePath);

        /**
         * @var $database DataRepositoryInterface
         */
        $database = app()->make(DataRepositoryInterface::class);
        $database->delete($entityId);
    }

}