<?php

/**
 *
 * This file is part of phpFastCache.
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Georges.L (Geolim4)  <contact@geolim4.com>
 * @author PastisD https://github.com/PastisD
 * @author Alexander (asm89) <iam.asm89@gmail.com>
 * @author Khoa Bui (khoaofgod)  <khoaofgod@gmail.com> http://www.phpfastcache.com
 *
 */

namespace Phpfastcache\Bundle\Twig\CacheExtension;

use Twig\Source;

/**
 * Cache strategy interface.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
interface CacheStrategyInterface
{
    /**
     * Fetch the block for a given key.
     *
     * @param mixed $key
     *
     * @param Source $sourceContext
     * @return mixed
     */
    public function fetchBlock($key, Source $sourceContext);

    /**
     * Generate a key for the value.
     *
     * @param string $annotation
     * @param mixed  $value
     *
     * @return mixed
     */
    public function generateKey($annotation, $value);

    /**
     * Save the contents of a rendered block.
     *
     * @param mixed  $key
     * @param string $block
     * @param int $generationTime
     *
     * @return mixed
     */
    public function saveBlock($key, $block, $generationTim, Source $sourceContext);
}
