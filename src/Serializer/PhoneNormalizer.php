<?php

namespace App\Serializer;

use App\Entity\Phone;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PhoneNormalizer implements ContextAwareNormalizerInterface
{

    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($phone, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($phone, $format, $context);

        // Here, add, edit, or delete some data:
        $data['href']['self'] = $this->router->generate('phones_show', [
            'id' => $phone->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Phone;
    }
}
