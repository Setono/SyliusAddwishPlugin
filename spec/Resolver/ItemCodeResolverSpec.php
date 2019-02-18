<?php

namespace spec\Setono\SyliusAddwishPlugin\Resolver;

use Setono\SyliusAddwishPlugin\Resolver\ItemCodeResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ItemCodeResolverSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(true);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ItemCodeResolver::class);
    }
}
