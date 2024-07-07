<?php

declare(strict_types=1);

namespace Tests\Unit\Validator;

use App\Validator\XmlItemValidator;
use Tests\BaseTestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

class XmlItemValidatorTest extends BaseTestCase
{
    private ObjectProphecy $logger;
    private XmlItemValidator $validator;

    protected function setUp(): void
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->validator = new XmlItemValidator($this->logger->reveal());
    }

    public function testValidateWithValidXml(): void
    {
        $xml = new SimpleXMLElement('
            <item>
                <entity_id>1</entity_id>
                <categoryName>Category</categoryName>
                <sku>SKU123</sku>
                <name>Item Name</name>
                <description>Description</description>
                <short_desc>Short Description</short_desc>
                <price>10.99</price>
                <link>http://example.com</link>
                <image>http://example.com/image.jpg</image>
                <brand>Brand</brand>
                <rating>5</rating>
                <caffeine_type>Caffeinated</caffeine_type>
                <count>10</count>
                <flavored>true</flavored>
                <seasonal>false</seasonal>
                <in_stock>true</in_stock>
                <facebook>100</facebook>
                <is_k_cup>1</is_k_cup>
            </item>
        ');

        $this->validator->validate($xml);
        $this->expectNotToPerformAssertions();
    }

    public function testValidateWithMissingProperty(): void
    {
        $xml = new SimpleXMLElement('
            <item>
                <entity_id>1</entity_id>
                <category_name>Category</category_name>
                <sku>SKU123</sku>
                <name>Item Name</name>
                <description>Description</description>
                <short_desc>Short Description</short_desc>
                <price>10.99</price>
                <link>http://example.com</link>
                <image>http://example.com/image.jpg</image>
                <brand>Brand</brand>
                <rating>5</rating>
                <caffeine_type>Caffeinated</caffeine_type>
                <count>10</count>
                <flavored>true</flavored>
                <seasonal>false</seasonal>
                <in_stock>true</in_stock>
                <facebook>100</facebook>
                <!-- Missing is_k_cup property -->
            </item>
        ');

        $this->logger->log('error', 'Missing required property on the model item: isKCup')
            ->shouldBeCalled();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required property on the model item: isKCup');

        $this->validator->validate($xml);
    }
}