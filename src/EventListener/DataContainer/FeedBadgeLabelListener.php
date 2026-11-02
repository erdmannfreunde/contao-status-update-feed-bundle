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

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\StringUtil;
use Contao\System;

/**
 * Appends the feeds an update is published in to the list label of
 * tl_status_update, e.g. "[Feed: Standard, Zeitfenster A]". The label callback of
 * the Status-Updates Bundle is wrapped, not replaced.
 */
#[AsHook('loadDataContainer')]
class FeedBadgeLabelListener
{
    /**
     * @var array<int, string>|null
     */
    private array|null $feedTitles = null;

    public function __construct(private readonly FeedPagesOptionsListener $feedPages)
    {
    }

    public function __invoke(string $table): void
    {
        if ('tl_status_update' !== $table) {
            return;
        }

        $original = $GLOBALS['TL_DCA'][$table]['list']['label']['label_callback'] ?? null;

        $GLOBALS['TL_DCA'][$table]['list']['label']['label_callback'] = function (array $row, string $label, ...$args) use ($original): string {
            if (\is_array($original)) {
                $label = System::importStatic($original[0])->{$original[1]}($row, $label, ...$args);
            } elseif (\is_callable($original)) {
                $label = $original($row, $label, ...$args);
            }

            return $label.$this->getBadge($row);
        };
    }

    /**
     * @param array<string, mixed> $row
     */
    private function getBadge(array $row): string
    {
        $this->feedTitles ??= ($this->feedPages)();

        $titles = array_filter(array_map(
            fn (int $id): string|null => $this->feedTitles[$id] ?? null,
            array_map('intval', StringUtil::deserialize($row['feed_pages'] ?? null, true)),
        ));

        if ([] === $titles) {
            return '';
        }

        return \sprintf('<span class="tl_gray" style="margin-left:6px;">[Feed: %s]</span>', implode(', ', $titles));
    }
}
