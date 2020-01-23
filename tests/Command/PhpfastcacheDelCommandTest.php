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
 *
 */

namespace Phpfastcache\Bundle\Tests\Command;

use Phpfastcache\Bundle\Command\PhpfastcacheDelCommand;
use Phpfastcache\CacheManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * PhpfastcacheCommandTest
 */
class PhpfastcacheDelCommandTest extends CommandTestCase
{
    public function testCommandSetCacheItem()
    {
        $value = \md5(\random_bytes(255));
        $ttl = \random_int(2, 10);
        $key = 'test' . \random_int(1000, 1999);

        $cacheItem = $this->phpfastcache
          ->get('filecache')
          ->getItem($key);
        $cacheItem->set($value)->expiresAfter($ttl);
        $this->phpfastcache->get('filecache')->save($cacheItem);

        $command = $this->application->find('phpfastcache:del');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
          'command' => $command->getName(),
          'driver' => 'filecache',
          'key' => $key,
          '--no-interaction' => true
        ]);

        $this->assertStringContainsString('OK] Cache item "' .  $key . '" has been deleted from cache', $commandTester->getDisplay());
    }

    /**
     * @return \Phpfastcache\Bundle\Command\PhpfastcacheClearCommand
     */
    protected function getCommand()
    {
        return new PhpfastcacheDelCommand($this->phpfastcache, $this->phpfastcacheParameters);
    }
}
