<?php

namespace App\EventListener;

use App\Annotation\Link;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use ReflectionObject;
use Symfony\Component\Routing\RouterInterface;

class LinkSerializationSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;
    private Reader $annotationReader;

    public function __construct(RouterInterface $router, Reader $annotationReader)
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        $object = $event->getObject();
        $annotations = $this->annotationReader->getClassAnnotations(new ReflectionObject($object));
        $links = [];
        foreach ($annotations as $annotation) {
            if ($annotations instanceof Link) {
                $uri = $this->router->generate(
                    $annotation->route,
                    $annotations->params
                );
                $links[$annotations->name] = $uri;
            }
        }
        $visitor->visitProperty(new StaticPropertyMetadata(User::class, '_links.self', $links), $links);
    }

    public function getSubscribedEvents(): array
    {
        return [
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json',
                'class' => 'App/Entity/User'
        ];
    }
}

