<?php

declare(strict_types=1);

/*
 * This file is part of erdmannfreunde/contao-status-update-feed-bundle.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license LGPL-3.0-or-later
 */

namespace ErdmannFreunde\ContaoStatusUpdateFeedBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ErdmannFreunde\ContaoLicenseBundle\ErdmannFreundeContaoLicenseBundle;
use ErdmannFreunde\ContaoStatusUpdateBundle\ErdmannFreundeContaoStatusUpdateBundle;
use ErdmannFreunde\ContaoStatusUpdateFeedBundle\ErdmannFreundeContaoStatusUpdateFeedBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ErdmannFreundeContaoStatusUpdateFeedBundle::class)
                ->setLoadAfter([ErdmannFreundeContaoStatusUpdateBundle::class, ErdmannFreundeContaoLicenseBundle::class]),
        ];
    }
}
