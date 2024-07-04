<?php

declare(strict_types=1);

namespace App\Reader;

use App\Logger\Logger;
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
     * @param string $file
     * /
     * @throws Exception
     */
    public function read(string $file): ItemCollection
    {
        if (!$this->reader->open($file)) {
            $this->logger->log(
                LogLevel::ERROR,
                sprintf('Xml File %s not found', $file),
                [
                    'facility' => Logger::FACILITY_IMPORTER
                ]
            );
            throw new Exception(sprintf('Xml File %s not found', $file));
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
}