<?php

declare(strict_types=1);

namespace App\Transformer;

use App\Model\Item;
use App\validator\XmlItemValidator;
use SimpleXMLElement;

readonly class XmlItemTransformer
{
    public function __construct(
        private XmlItemValidator $validator
    ) {
    }

    public function transform(SimpleXMLElement $element): Item
    {
        $this->validator->validate($element);
        return new Item(
            (int)$element->entity_id,
            (string)$element->CategoryName,
            (string)$element->sku,
            (string)$element->name,
            $element->description ? (string)$element->description : null,
            (string)$element->shortdesc,
            (float)$element->price,
            (string)$element->link,
            (string)$element->image,
            (string)$element->Brand,
            (int)$element->Rating,
            $element->CaffeineType ? (string)$element->CaffeineType : null,
            (int)$element->Count,
            $this->toBoolean((string)$element->Flavored),
            $this->toBoolean((string)$element->Seasonal),
            $this->toBoolean((string)$element->Instock),
            (int)$element->Facebook,
            strtolower((string)$element->IsKCup) === '1'
        );
    }

    private function toBoolean(string $value): bool
    {
        $normalizedValue = strtolower(trim($value));
        return in_array($normalizedValue, ['yes', 'true', '1', 'y'], true);
    }
}