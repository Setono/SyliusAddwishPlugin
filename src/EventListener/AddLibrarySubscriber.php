<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\SyliusAddwishPlugin\Tag\Tags;
use Setono\TagBagBundle\Tag\ScriptTag;
use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddLibrarySubscriber extends TagSubscriber
{
    /**
     * @var string
     */
    private $partnerId;

    /**
     * @param TagBagInterface $tagBag
     * @param string $partnerId
     */
    public function __construct(
        TagBagInterface $tagBag,
        string $partnerId
    ) {
        parent::__construct($tagBag);

        $this->partnerId = $partnerId;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                'addLibrary',
            ],
        ];
    }

    public function addLibrary(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Only add the library on 'real' page loads, not AJAX requests like add to cart
        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $this->tagBag->add(new ScriptTag('var _awev=window._awev||[];', Tags::TAG_EVENT_HANDLER), TagBagInterface::SECTION_HEAD);

        $this->tagBag->add(new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/library.html.twig',
            TagInterface::TYPE_HTML,
            Tags::TAG_LIBRARY,
            ['partner_id' => $this->partnerId]
        ), TagBagInterface::SECTION_HEAD);
    }
}
