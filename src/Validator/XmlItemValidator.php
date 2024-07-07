<?php

declare(strict_types=1);

namespace App\Validator;

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
        $normalizedElement = $this->normalizeXmlElement($element);
        $itemReflection = new ReflectionClass(Item::class);
        $properties = $itemReflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            $normalizedPropertyName = strtolower($property->getName());
            if (!array_key_exists($normalizedPropertyName, $normalizedElement)) {
                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Missing required property on the model item: %s', $property->getName())
                );
                throw new InvalidArgumentException(
                    sprintf('Missing required property on the model item: %s', $property->getName())
                );
            }
        }
    }

    private function convertSnakeCaseToCamelCase(string $input): string
    {
        return lcfirst(str_replace('_', '', ucwords($input, '_')));
    }

    /**
     * @param SimpleXMLElement $element
     * @return array<string, string>
     */
    private function normalizeXmlElement(SimpleXMLElement $element): array
    {
        $normalized = [];
        foreach ($element as $key => $value) {
            $normalizedKey = $this->convertSnakeCaseToCamelCase(strtolower($key));
            $normalized[strtolower($normalizedKey)] = (string) $value;
        }
        return $normalized;
    }
}