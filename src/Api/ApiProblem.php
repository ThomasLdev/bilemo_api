<?php

namespace App\Api;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    const TYPE_VALIDATION_ERROR = "validation_error";
    const TYPE_INVALID_REQUEST_BODY_FORMAT = "invalid_body_format";

    private static array $titles = [
        self::TYPE_VALIDATION_ERROR => "There was a validation error",
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => "Invalid JSON format sent"
    ];

    private ?int $statusCode;

    private ?string $type;

    private array $extraData = [];

    private ?string $title;

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
                throw new \InvalidArgumentException('No title for type '.$type);
            }
            $title = self::$titles[$type];
        }

        $this->type = $type;
        $this->title = $title;
    }

    public function toArray(): array
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

    public function set($name, $value): self
    {
        $this->extraData[$name] = $value;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}