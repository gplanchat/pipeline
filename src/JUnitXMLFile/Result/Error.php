<?php

namespace Kiboko\Component\JUnitXMLFile\Result;

class Error
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $message;

    /**
     * @param string $type
     * @param string $message
     */
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public static function consume(\XMLReader $xml): iterable
    {
        while ($xml->name === 'error' && \XMLReader::ELEMENT) {
            yield new self($xml->getAttribute('type'), $xml->readString());

            if (!$xml->read()) {
                break;
            }
        }
    }
}
