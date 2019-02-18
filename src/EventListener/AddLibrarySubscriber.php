<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\EventListener;

use Setono\TagBagBundle\Factory\TwigTagFactory;
use Setono\TagBagBundle\HttpFoundation\Session\Tag\TagBagInterface;
use Setono\TagBagBundle\Tag\TagInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddLibrarySubscriber extends TagSubscriber
{
    /**
     * @var string
     */
    private $partnerId;

    /**
     * @var TwigTagFactory
     */
    private $twigTagFactory;

    /**
     * @param TagBagInterface $tagBag
     * @param TwigTagFactory $twigTagFactory
     * @param string $partnerId
     */
    public function __construct(
        TagBagInterface $tagBag,
        TwigTagFactory $twigTagFactory,
        string $partnerId
    ) {
        parent::__construct($tagBag);

        $this->twigTagFactory = $twigTagFactory;
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
     *
     * @throws \Twig\Error\Error
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

        $tag = $this->twigTagFactory->create('@SetonoSyliusAddwishPlugin/Tag/library.js.twig', TagInterface::TYPE_SCRIPT, [
            'partner_id' => $this->partnerId,
        ]);

        $this->tagBag->addScript($tag, TagBagInterface::SECTION_BODY_BEGIN);
    }
}
