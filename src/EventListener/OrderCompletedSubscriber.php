<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBagBundle\Factory\TwigTagFactory;
use Setono\TagBagBundle\HttpFoundation\Session\Tag\TagBagInterface;
use Setono\TagBagBundle\Tag\TagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderCompletedSubscriber extends TagSubscriber
{
    /**
     * @var TwigTagFactory
     */
    private $twigTagFactory;

    public function __construct(TagBagInterface $tagBag, TwigTagFactory $twigTagFactory)
    {
        parent::__construct($tagBag);

        $this->twigTagFactory = $twigTagFactory;
    }

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
     *
     * @throws \Twig\Error\Error
     */
    public function addScript(GenericEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        // As an alternative, we can use items_purchased.js.twig, but
        // it looks like less detailed
        $tag = $this->twigTagFactory->create('@SetonoSyliusAddwishPlugin/Tag/items_purchased.html.twig', TagInterface::TYPE_SCRIPT, [
            'order' => $order,
        ]);

        $this->tagBag->add($tag, TagBagInterface::SECTION_BODY_BEGIN);
    }
}
