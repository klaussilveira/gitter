    const CONTENTS = 'Your mother is so ugly, glCullFace always returns TRUE.';
    protected static $tmpdir;
    protected static $cached_repos;

        $cached_dir = self::$tmpdir . DIRECTORY_SEPARATOR . 'cache';
        $fs->mkdir($cached_dir);
        self::$cached_repos = $cached_dir . DIRECTORY_SEPARATOR . 'repos.json';
            'hidden' => array(self::$tmpdir . '/hiddenrepo'),
            'ini.file' => 'config.ini',
            'cache.repos' =>  self::$cached_repos

        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file.txt', self::CONTENTS);
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file1.txt', self::CONTENTS);
        file_put_contents(self::$tmpdir . '/testrepo/test_file2.txt', self::CONTENTS);
        file_put_contents(self::$tmpdir . '/testrepo/test_file3.txt', self::CONTENTS);
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file4.txt', self::CONTENTS);
        file_put_contents(self::$tmpdir . '/testrepo/test_file5.txt', self::CONTENTS);
        file_put_contents(self::$tmpdir . '/testrepo/test_file6.txt', self::CONTENTS);
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file7.txt', self::CONTENTS);
        file_put_contents(self::$tmpdir . '/testrepo/test_file8.txt', self::CONTENTS);
        file_put_contents(self::$tmpdir . '/testrepo/test_file9.txt', self::CONTENTS);
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $this->assertEquals(self::CONTENTS, $blob);
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        $this->assertEquals($blame[1]['line'], PHP_EOL . ' ' . self::CONTENTS);
        $repository = $this->client->getRepositoryCached(self::$tmpdir, 'testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test file10.txt', self::CONTENTS);
    }

        # Following will not work with cache list
        #$nested_repositories = $this->client->getRepositories($nested_dir);
        #$this->assertCount(1, $nested_repositories, 'Only one nested repository');

        $this->assertContains('nestedrepo', array_keys($all_repositories),
                'Nested repository is found in all repositories');
