<?php

declare(strict_types=1);

namespace App\Reader;

use App\Model\ItemCollection;
use App\Transformer\XmlItemTransformer;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use SimpleXMLElement;
use XMLReader;

class XMLDataReader implements DataReaderInterface
{
    public function __construct(
        private readonly XMLReader $reader,
        private readonly XmlItemTransformer $transformer,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * /**
     *  There are different methods to parse the content of the XML,
     *  also depends on whether the XML file is
     *  very large and how important performance is
     *
     * @param string $filePath
     * /
     * @throws Exception
     */
    public function read(string $filePath): ItemCollection
    {
        if (!$this->reader->open($filePath)) {
            $this->handleXmlError($filePath, 'Unable to open XML file');
        }

        $collection = new ItemCollection();
        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == 'item') {
                $outerXml = $this->reader->readOuterXml();
                $simpleXmlElement = new SimpleXMLElement($outerXml);
                $item = $this->transformer->transform($simpleXmlElement);
                $collection->push($item);
            }
        }

        $this->reader->close();

        return $collection;
    }


    /**
     * Handles XML file opening errors.
     *
     * @param string $fileName The file name
     * @param string $message The error message
     * @throws Exception
     */
    private function handleXmlError(string $fileName, string $message): void
    {
        $this->logger->log(LogLevel::ERROR, $message, ['file' => $fileName]);
        throw new Exception("XML file '{$fileName}' not found or could not be opened");
    }
}