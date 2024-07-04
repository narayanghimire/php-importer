<?php

declare(strict_types=1);

namespace App\Reader;

use App\constants\Constants;
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
        $fileName = __DIR__.'/../../resources/'.basename($file);
        $fileExists = file_exists($fileName);
        if (!$fileExists) {
            $fileName =__DIR__.'/../../resources/'.basename(Constants::DEFUALT_XML_FILE);
        }
        if (!$this->reader->open($fileName)) {
            $this->logger->log(
                LogLevel::ERROR,
                sprintf('unable to open Xml File %s ', $fileName),
                [
                    'facility' => Logger::FACILITY_IMPORTER
                ]
            );
            throw new Exception(sprintf('Xml File %s not found', $fileName));
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