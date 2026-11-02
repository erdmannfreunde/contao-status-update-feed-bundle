<?php

declare(strict_types=1);

/*
 * This file is part of erdmannfreunde/contao-status-update-feed-bundle.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license LGPL-3.0-or-later
 */

namespace ErdmannFreunde\ContaoStatusUpdateFeedBundle;

use ErdmannFreunde\ContaoStatusUpdateFeedBundle\DependencyInjection\ErdmannFreundeContaoStatusUpdateFeedExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ErdmannFreundeContaoStatusUpdateFeedBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ErdmannFreundeContaoStatusUpdateFeedExtension
    {
        return new ErdmannFreundeContaoStatusUpdateFeedExtension();
    }
}
