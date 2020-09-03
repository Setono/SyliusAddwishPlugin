<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\Tag\TwigTag;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderCompletedSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => [
                'addScript',
            ],
        ];
    }

    public function addScript(GenericEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        // As an alternative, we can use order_completed_js.html.twig, but
        // it looks like less detailed
        $twigTag = new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/order_completed.html.twig',
            ['order' => $order]
        );
        $twigTag->setSection(TagInterface::SECTION_BODY_END);
        $this->tagBag->addTag($twigTag);
    }
}
