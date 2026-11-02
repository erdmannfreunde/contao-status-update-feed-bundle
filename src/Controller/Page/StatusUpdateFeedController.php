<?php

declare(strict_types=1);

/*
 * This file is part of erdmannfreunde/contao-status-update-feed-bundle.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license LGPL-3.0-or-later
 */

namespace ErdmannFreunde\ContaoStatusUpdateFeedBundle\Controller\Page;

use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsPage;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use ErdmannFreunde\ContaoStatusUpdateFeedBundle\License\LicenseGate;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Outputs all status updates assigned to this feed page (field "feed_pages") as
 * JSON feed (v1), which the Status-Updates Bundle can subscribe to via its
 * "feed_url" option.
 */
#[AsPage(type: 'status_update_feed', path: '', contentComposition: false, urlSuffix: '.json')]
class StatusUpdateFeedController extends AbstractController
{
    /**
     * Matches the longest "show after" option, so items only leave the feed once
     * subscribers would no longer display them anyway.
     */
    private const KEEP_DAYS = 30;

    public function __construct(
        private readonly Connection $connection,
        private readonly InsertTagParser $insertTagParser,
        #[Autowire(service: 'contao.string.html_decoder')]
        private readonly HtmlDecoder $htmlDecoder,
        private readonly LicenseGate $licenseGate,
    ) {
    }

    public function __invoke(Request $request, PageModel $pageModel): Response
    {
        $this->initializeContaoFramework();

        // An error instead of an empty feed: subscribers keep their last state instead
        // of removing all announcements.
        if (!$this->licenseGate->isLicensed()) {
            $response = new JsonResponse(
                [
                    'error' => 'Für diesen Feed ist keine gültige Lizenz hinterlegt.',
                    'vendor_url' => $this->licenseGate->vendorUrl(),
                ],
                Response::HTTP_FORBIDDEN,
            );
            $response->headers->set('X-Robots-Tag', 'noindex');

            return $response;
        }

        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, tstamp, title, description, date, show_days_before, show_days_after, completed, feed_pages
             FROM tl_status_update
             WHERE published = 1 AND feed_pages IS NOT NULL AND date >= :since
             ORDER BY date ASC',
            ['since' => strtotime(\sprintf('-%d days', self::KEEP_DAYS), strtotime(date('Y-m-d 00:00:00')))],
        );

        $baseUrl = $request->getSchemeAndHttpHost().$request->getBasePath().'/';
        $items = [];

        foreach ($rows as $row) {
            if (!\in_array((int) $pageModel->id, array_map('intval', StringUtil::deserialize($row['feed_pages'], true)), true)) {
                continue;
            }

            $description = $this->insertTagParser->replaceInline((string) ($row['description'] ?? ''));

            $items[] = [
                'id' => (int) $row['id'],
                'updated' => (int) $row['tstamp'],
                'title' => $this->htmlDecoder->inputEncodedToPlainText((string) $row['title'], true),
                // Subscribers only accept absolute https/mailto links
                'description' => Controller::convertRelativeUrls($description, $baseUrl, true),
                'date' => date('Y-m-d', (int) $row['date']),
                'show_days_before' => (string) $row['show_days_before'],
                'show_days_after' => (string) $row['show_days_after'],
                'completed' => (bool) $row['completed'],
            ];
        }

        $response = new JsonResponse(['version' => 1, 'items' => $items]);
        $response->headers->set('X-Robots-Tag', 'noindex');

        $this->tagResponse('contao.db.tl_status_update');

        return $this->setCacheHeaders($response, $pageModel);
    }
}
