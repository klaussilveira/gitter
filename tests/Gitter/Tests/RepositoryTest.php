
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file1.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        file_put_contents(self::$tmpdir . '/testrepo/test_file2.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        file_put_contents(self::$tmpdir . '/testrepo/test_file3.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file4.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        file_put_contents(self::$tmpdir . '/testrepo/test_file5.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        file_put_contents(self::$tmpdir . '/testrepo/test_file6.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test_file7.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        file_put_contents(self::$tmpdir . '/testrepo/test_file8.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        file_put_contents(self::$tmpdir . '/testrepo/test_file9.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');

        $commits = $repository->getCommits();
        $hash = $commits[0]->getHash();
        $repository->checkout($hash);
        $new_branch = $repository->getCurrentBranch();
        $this->assertTrue($new_branch === NULL);

        $repository->checkout($branch);
    }

    public function testIsGettingBranchesWhenHeadIsDetached()
    {
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $commits = $repository->getCommits();
        $current_branch = $repository->getCurrentBranch();
        $hash = $commits[0]->getHash();
        $repository->checkout($hash);
        $branches = $repository->getBranches();
        $this->assertTrue(count($branches) === 3);

        $branch = $repository->getHead('develop');
        $repository->checkout($current_branch);
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $this->assertEquals('Your mother is so ugly, glCullFace always returns TRUE.', $blob);
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        $this->assertEquals($blame[1]['line'], PHP_EOL . ' Your mother is so ugly, glCullFace always returns TRUE.');
        $repository = $this->client->getRepository(self::$tmpdir . '/testrepo');
        file_put_contents(self::$tmpdir . '/testrepo/test file10.txt', 'Your mother is so ugly, glCullFace always returns TRUE.');
	}
        $nested_repositories = $this->client->getRepositories($nested_dir);
        $this->assertCount(1, $nested_repositories, 'Only one nested repository');
        $this->assertContains($nested_repositories[0], $all_repositories, 'Nested repository is found in all repositories');