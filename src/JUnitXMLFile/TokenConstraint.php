<?php

namespace Kiboko\Component\JUnitXMLFile;

class TokenConstraint
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @param int $token
     * @param null|string $name
     */
    public function __construct(int $token, ?string $name = null)
    {
        $this->token = $token;
        $this->name = $name;
    }
}
