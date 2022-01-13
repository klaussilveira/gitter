<?php

namespace Gitter\Tests;

use Gitter\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ClientTest extends TestCase
{
    public static $tmpdir;
    protected $client;

    public static function setUpBeforeClass(): void
    {
        if (getenv('TMP')) {
            self::$tmpdir = getenv('TMP');
        } elseif (getenv('TMPDIR')) {
            self::$tmpdir = getenv('TMPDIR');
        } else {
            self::$tmpdir = '/tmp';
        }

        self::$tmpdir .= '/gitlist_' . md5(time() . mt_rand());

        $fs = new Filesystem();
        $fs->mkdir(self::$tmpdir);

        if (!is_writable(self::$tmpdir)) {
            $this->markTestSkipped('There are no write permissions in order to create test repositories.');
        }
    }

    public static function tearDownAfterClass(): void
    {
        $fs = new Filesystem();
        $fs->remove(self::$tmpdir);
    }

    public function setUp(): void
    {
        if (!is_writable(self::$tmpdir)) {
            $this->markTestSkipped('There are no write permissions in order to create test repositories.');
        }

        $path = getenv('GIT_CLIENT') ?: null;
        $this->client = new Client($path);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsNotAbleToGetUnexistingRepository()
    {
        $this->client->getRepository(self::$tmpdir . '/testrepo');
    }

    public function testIsParsingGitVersion()
    {
        $version = $this->client->getVersion();
        $this->assertNotEmpty($version);
    }

    public function testIsCreatingRepository()
    {
        $repository = $this->client->createRepository(self::$tmpdir . '/testrepo');
        $fs = new Filesystem();
        $fs->remove(self::$tmpdir . '/testrepo/.git/description');
        $this->assertRegExp('/nothing to commit/', $repository->getClient()->run($repository, 'status'));
    }

    public function testIsCreatingBareRepository()
    {
        $repository = $this->client->createRepository(self::$tmpdir . '/testbare', true);
        $this->assertInstanceOf('Gitter\Repository', $repository);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsNotAbleToCreateRepositoryDueToExistingOne()
    {
        $this->client->createRepository(self::$tmpdir . '/testrepo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsNotOpeningHiddenRepositories()
    {
        $this->client->getRepository(self::$tmpdir . '/hiddenrepo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsCatchingGitCommandErrors()
    {
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository->getClient()->run($repository, 'wrong');
    }
}
