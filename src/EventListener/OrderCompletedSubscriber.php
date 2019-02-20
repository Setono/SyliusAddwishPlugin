<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\SyliusAddwishPlugin\Tag\Tags;
use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
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

    /**
     * @param GenericEvent $event
     */
    public function addScript(GenericEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        // As an alternative, we can use order_completed.js.twig, but
        // it looks like less detailed
        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/order_completed.html.twig',
            TagInterface::TYPE_HTML,
            Tags::TAG_ORDER_COMPLETED,
            ['order' => $order]
        ), TagBagInterface::SECTION_BODY_END);
    }
}
