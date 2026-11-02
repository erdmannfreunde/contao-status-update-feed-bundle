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
use Doctrine\DBAL\Connection;

/**
 * Offers every feed page as option, labelled with its (freely chosen) page title.
 */
#[AsCallback(table: 'tl_status_update', target: 'fields.feed_pages.options')]
class FeedPagesOptionsListener
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * @return array<int, string>
     */
    public function __invoke(): array
    {
        return $this->connection->fetchAllKeyValue(
            "SELECT id, title FROM tl_page WHERE type = 'status_update_feed' ORDER BY title",
        );
    }
}
