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
declare(strict_types=1);

namespace Phpfastcache\Bundle\Twig\CacheExtension\CacheStrategy;

use Phpfastcache\Bundle\DataCollector\CacheCollector;
use Phpfastcache\Bundle\Twig\CacheExtension\CacheProviderInterface;
use Phpfastcache\Bundle\Twig\CacheExtension\CacheStrategyInterface;
use Phpfastcache\Bundle\Twig\CacheExtension\Exception\InvalidCacheLifetimeException;
use Twig\Source;

/**
 * Strategy for caching with a pre-defined lifetime.
 *
 * The value passed to the strategy is the lifetime of the cache block in
 * seconds.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class LifetimeCacheStrategy implements CacheStrategyInterface
{
    /**
     * @var string
     */
    private $twigCachePrefix = '_TwigCacheLCS_';

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var \Phpfastcache\Bundle\Twig\CacheExtension\CacheProviderInterface
     */
    private $cache;

    /**
     * @var \Phpfastcache\Bundle\DataCollector\CacheCollector
     */
    private $cacheCollector;

    /**
     * LifetimeCacheStrategy constructor.
     * @param \Phpfastcache\Bundle\Twig\CacheExtension\CacheProviderInterface $cache
     * @param \Phpfastcache\Bundle\DataCollector\CacheCollector $cacheCollector
     * @param array $config
     */
    public function __construct(CacheProviderInterface $cache, CacheCollector $cacheCollector = null, array $config)
    {
        $this->cache = $cache;
        $this->cacheCollector = $cacheCollector;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchBlock($key, Source $sourceContext)
    {
        $generationTimeMc = \microtime(true);
        $cacheData = $this->cache->fetch($key[ 'key' ]);
        $generationTime = \microtime(true) - $generationTimeMc;
        $unprefixedKey = \substr($key[ 'key' ], \strlen($this->twigCachePrefix));

        if ($this->cacheCollector instanceof CacheCollector) {
            $this->cacheCollector->setTwigCacheBlock($unprefixedKey, [
              'cacheHit' => $cacheData !== null,
              'cacheTtl' => $key[ 'lifetime' ],
              'cacheSize' => mb_strlen((string)$cacheData),
              'cacheGenTime' => $generationTime,
              'cacheFileName' => $sourceContext->getName(),
              'cacheFilePath' => $sourceContext->getPath(),
            ]);
        }

        if (!empty($cacheData) && $this->config[ 'twig_block_debug' ]) {
            return "<!-- BEGIN CACHE BLOCK OUTPUT '{$unprefixedKey}' -->\n{$cacheData}\n<!-- // END CACHE BLOCK OUTPUT '{$unprefixedKey}' -->";
        }

        return $cacheData;
    }

    /**
     * {@inheritDoc}
     */
    public function generateKey($annotation, $value)
    {
        if (!\is_numeric($value)) {
            throw new InvalidCacheLifetimeException($value);
        }

        return [
          'lifetime' => $value,
          'key' => $this->twigCachePrefix . $annotation,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function saveBlock($key, $block, $generationTime, Source $sourceContext)
    {
        $unprefixedKey = \substr($key[ 'key' ], \strlen($this->twigCachePrefix));

        if ($this->cacheCollector instanceof CacheCollector) {
            $this->cacheCollector->setTwigCacheBlock($unprefixedKey, [
              'cacheHit' => false,
              'cacheTtl' => $key[ 'lifetime' ],
              'cacheSize' => \mb_strlen((string)$block),
              'cacheGenTime' => $generationTime,
              'cacheFileName' => $sourceContext->getName(),
              'cacheFilePath' => $sourceContext->getPath(),
            ]);
        }

        return $this->cache->save($key[ 'key' ], $block, $key[ 'lifetime' ]);
    }
}
