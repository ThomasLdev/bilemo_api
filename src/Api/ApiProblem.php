<?php

namespace App\Api;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    const TYPE_VALIDATION_ERROR = "validation_error";
    const TYPE_INVALID_REQUEST_BODY_FORMAT = "invalid_body_format";
    const TYPE_RESSOURCE_NOT_FOUND = "forbidden_ressource";

    private static array $titles = [
        self::TYPE_VALIDATION_ERROR => "There was a validation error",
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => "Invalid JSON format sent",
        self::TYPE_RESSOURCE_NOT_FOUND => "The selected ressource does not exist"
    ];

    private $statusCode;

    private $type;

    private $extraData = [];

    private $title;

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;

        if (null === $type) {
            $type = 'about:blank';
            $title = Response::$statusTexts[$statusCode] ?? 'Unknown status code :(';
        } elseif (!isset(self::$titles[$type])) {
            throw new InvalidArgumentException('No title for type' . $type);
        } else {
            if (!isset(self::$titles[$type])) {
                throw new InvalidArgumentException('No title for type '.$type);
            }
            $title = self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title
            ]
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
