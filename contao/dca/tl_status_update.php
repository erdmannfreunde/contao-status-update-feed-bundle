<?php

declare(strict_types=1);

/*
 * This file is part of erdmannfreunde/contao-status-update-feed-bundle.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Options: all pages of type "status_update_feed" (see FeedPagesOptionsListener)
$GLOBALS['TL_DCA']['tl_status_update']['fields']['feed_pages'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_status_update']['feed_pages'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['multiple' => true, 'doNotCopy' => true, 'tl_class' => 'clr'],
    'sql' => 'blob NULL',
];

// Button callback: LicenseButtonListener
$GLOBALS['TL_DCA']['tl_status_update']['list']['global_operations'] = [
    'license' => ['href' => '', 'class' => 'header_license'],
] + ($GLOBALS['TL_DCA']['tl_status_update']['list']['global_operations'] ?? []);

PaletteManipulator::create()
    ->addField('feed_pages', 'publish_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_status_update')
;
