<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBagBundle\Tag\TagInterface;
use Setono\TagBagBundle\Tag\TwigTag;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
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
            KernelEvents::RESPONSE => [
                'addLibrary',
            ],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function addLibrary(FilterResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Only add the library on 'real' page loads, not AJAX requests like add to cart
        if ($event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        // we don't want to add the library code on redirects
        $statusCode = $event->getResponse()->getStatusCode();
        if (($statusCode < 200 || $statusCode > 299) && !in_array($statusCode, [404, 500], true)) {
            return;
        }

        $this->tagBag->add(new TwigTag('@SetonoSyliusAddwishPlugin/Tag/library.js.twig', TagInterface::TYPE_SCRIPT, [
            'partner_id' => $this->partnerId,
        ]), TagBagInterface::SECTION_BODY_BEGIN);
    }
}
