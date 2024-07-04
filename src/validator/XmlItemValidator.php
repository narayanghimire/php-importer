<?php

declare(strict_types=1);

namespace App\validator;

use App\Model\Item;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use ReflectionClass;
use ReflectionProperty;
use SimpleXMLElement;

class XmlItemValidator
{
    public function __construct(
        private LoggerInterface $logger
    ){}
    public function validate(SimpleXMLElement $element): void
    {
        $itemReflection = new ReflectionClass(Item::class);
        $properties = $itemReflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $this->convertCamelCaseToSnakeCase($property->getName());
            if (!isset($element->{$name})) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Missing required property on the model item: %s', $name)
                );
                throw new InvalidArgumentException(       sprintf('Missing required property on the model item: %s', $name),);
            }

        }
    }

    private function convertCamelCaseToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}