<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBag\Tag\ScriptTag;
use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\Tag\TwigTag;
use Setono\TagBag\TagBagInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddLibrarySubscriber extends TagSubscriber
{
    private string $partnerId;

    public function __construct(TagBagInterface $tagBag, string $partnerId)
    {
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

    public function addLibrary(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Only add the library on 'real' page loads, not AJAX requests like add to cart
        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $scriptTag = new ScriptTag('var _awev=window._awev||[];');
        $scriptTag->setSection(TagInterface::SECTION_HEAD);
        $this->tagBag->addTag($scriptTag);

        $twigTag = new TwigTag(
            '@SetonoSyliusAddwishPlugin/Tag/library.html.twig',
            ['partner_id' => $this->partnerId]
        );
        $twigTag->setSection(TagInterface::SECTION_HEAD);
        $this->tagBag->addTag($twigTag);
    }
}
