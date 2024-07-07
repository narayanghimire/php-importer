<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use App\Model\Item;
use App\Transformer\XmlItemTransformer;
use App\Validator\XmlItemValidator;
use Tests\BaseTestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;
use SimpleXMLElement;


class XmlItemTransformerTest extends BaseTestCase
{
    private ObjectProphecy $validatorMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->prophesize(XmlItemValidator::class);
    }

    public function testTransformValidData(): void
    {
        $xml = new SimpleXMLElement('
            <item>
                <entity_id>1</entity_id>
                <CategoryName>Category</CategoryName>
                <sku>SKU123</sku>
                <name>Item Name</name>
                <description>Description</description>
                <shortdesc>Short Description</shortdesc>
                <price>10.99</price>
                <link>http://example.com</link>
                <image>http://example.com/image.jpg</image>
                <Brand>Brand</Brand>
                <Rating>5</Rating>
                <CaffeineType>Caffeinated</CaffeineType>
                <Count>10</Count>
                <Flavored>yes</Flavored>
                <Seasonal>no</Seasonal>
                <Instock>yes</Instock>
                <Facebook>100</Facebook>
                <is_k_cup>0</is_k_cup>
            </item>
        ');
        $transformer  = new XmlItemTransformer($this->validatorMock->reveal());
        $item = $transformer->transform($xml);
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals(1, $item->getEntityId());
        $this->assertEquals('Category', $item->getCategoryName());
        $this->assertEquals('SKU123', $item->getSku());
        $this->assertEquals('Item Name', $item->getName());
        $this->assertEquals('Description', $item->getDescription());
        $this->assertEquals('Short Description', $item->getShortDesc());
        $this->assertEquals(10.99, $item->getPrice());
        $this->assertEquals('http://example.com', $item->getLink());
        $this->assertEquals('http://example.com/image.jpg', $item->getImage());
        $this->assertEquals('Brand', $item->getBrand());
        $this->assertEquals(5, $item->getRating());
        $this->assertEquals('Caffeinated', $item->getCaffeineType());
        $this->assertEquals(10, $item->getCount());
        $this->assertTrue($item->isFlavored());
        $this->assertFalse($item->isSeasonal());
        $this->assertTrue($item->isInStock());
        $this->assertEquals(100, $item->getFacebook());
        $this->assertFalse($item->isKCup());
    }

    public function testTransformInvalidData(): void
    {
        $xml = new SimpleXMLElement('
            <item>
                <entity_id>1</entity_id>
                <CategoryName>Category</CategoryName>
                <sku>SKU123</sku>
                <name>Item Name</name>
                <!-- Missing required property: description -->
                <shortdesc>Short Description</shortdesc>
                <price>10.99</price>
                <link>http://example.com</link>
                <image>http://example.com/image.jpg</image>
                <Brand>Brand</Brand>
                <Rating>5</Rating>
                <CaffeineType>Caffeinated</CaffeineType>
                <Count>10</Count>
                <Flavored>yes</Flavored>
                <Seasonal>no</Seasonal>
                <Instock>yes</Instock>
                <Facebook>100</Facebook>
            </item>
        ');

        $this->validatorMock
            ->validate($xml)
            ->shouldBeCalled()
            ->willThrow(
                new InvalidArgumentException('Missing required property on the model item: description')
            );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required property on the model item: description');

        $transformer  = new XmlItemTransformer($this->validatorMock->reveal());
         $transformer->transform($xml);
    }
}