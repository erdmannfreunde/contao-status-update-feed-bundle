<?php

declare(strict_types=1);

/*
 * This file is part of erdmannfreunde/contao-status-update-feed-bundle.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license LGPL-3.0-or-later
 */

namespace ErdmannFreunde\ContaoStatusUpdateFeedBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use ErdmannFreunde\ContaoLicenseBundle\View\LicenseLink;

/**
 * Renders the "Lizenz" global operation, which links to the (hidden) license
 * overview of the License Bundle.
 */
#[AsCallback(table: 'tl_status_update', target: 'list.global_operations.license.button')]
class LicenseButtonListener
{
    public function __construct(private readonly LicenseLink $licenseLink)
    {
    }

    public function __invoke(): string
    {
        return $this->licenseLink->render('header_license');
    }
}
