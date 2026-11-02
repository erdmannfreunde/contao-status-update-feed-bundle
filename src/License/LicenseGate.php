<?php

declare(strict_types=1);

/*
 * This file is part of erdmannfreunde/contao-status-update-feed-bundle.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license LGPL-3.0-or-later
 */

namespace ErdmannFreunde\ContaoStatusUpdateFeedBundle\License;

use ErdmannFreunde\ContaoLicenseBundle\License\LicenseManager;
use ErdmannFreunde\ContaoLicenseBundle\License\ProductRegistry;

/**
 * The bundle is commercial: without a valid license key the feeds are not
 * delivered (there is no free tier).
 */
final class LicenseGate
{
    public const PRODUCT = 'status-update-feed';

    public function __construct(
        private readonly LicenseManager $licenses,
        private readonly ProductRegistry $registry,
    ) {
    }

    public function isLicensed(): bool
    {
        return $this->licenses->isValid(self::PRODUCT);
    }

    /**
     * Product page with details and ordering (vendorUrl of the product registration).
     */
    public function vendorUrl(): string|null
    {
        return $this->registry->get(self::PRODUCT)?->getVendorUrl();
    }
}
