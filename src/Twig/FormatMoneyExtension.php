<?php

declare(strict_types=1);

namespace Setono\SyliusAddwishPlugin\Twig;

use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FormatMoneyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('setono_sylius_addwish_format_money', [$this, 'formatMoney']),
        ];
    }

    /**
     * @throws StringsException
     */
    public function formatMoney(int $money): string
    {
        return sprintf('%01.2F', $money / 100);
    }
}
