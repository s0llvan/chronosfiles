<?php
namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use App\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegistrationSubscriber implements EventSubscriberInterface
{
    private $container;
    private $entityManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::USER_REGISTERED => 'onUserRegistrated',
        ];
    }

    public function onUserRegistrated(GenericEvent $event): void
    {
        /** @var User $user */
        $user = $event->getSubject();
    }
}
