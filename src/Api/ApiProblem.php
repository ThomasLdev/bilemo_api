<?php


namespace App\Api;


class ApiProblem
{
    private $statusCode;

    private $type;

    private $title;

    private array $extraData = [];

    public function __construct($statusCode, $type, $title)
    {
        $this->statusCode = $statusCode;
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->extraData,
            [
                'status'    => $this->statusCode,
                'type'      => $this->type,
                'title'     => $this->title
            ]
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}