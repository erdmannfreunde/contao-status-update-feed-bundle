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
use Contao\Input;
use Contao\Message;
use ErdmannFreunde\ContaoStatusUpdateFeedBundle\License\LicenseGate;

/**
 * Tells editors in the status update overview that the feeds are not delivered as
 * long as no valid license key is stored.
 */
#[AsCallback(table: 'tl_status_update', target: 'config.onload')]
class LicenseNoticeListener
{
    public function __construct(private readonly LicenseGate $licenseGate)
    {
    }

    public function __invoke(): void
    {
        // Only in the overview, so the notice does not pile up across redirects
        if (Input::get('act') || Input::get('key') || $this->licenseGate->isLicensed()) {
            return;
        }

        $text = $GLOBALS['TL_LANG']['tl_status_update']['feed_license_missing'] ?? 'No valid license key is stored for the status update feed.';

        if ($url = $this->licenseGate->vendorUrl()) {
            $label = $GLOBALS['TL_LANG']['tl_status_update']['feed_license_more'] ?? 'More information';
            $text .= \sprintf(' <a href="%s" target="_blank" rel="noopener" style="text-decoration:underline">%s</a>', htmlspecialchars($url, ENT_QUOTES), htmlspecialchars($label, ENT_QUOTES));
        }

        Message::addError($text);
    }
}
