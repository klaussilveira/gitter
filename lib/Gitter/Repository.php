<?php

/*
 * This file is part of the Gitter library.
 *
 * (c) Klaus Silveira <klaussilveira@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitter;

use Gitter\Constants\Commands;
use Gitter\Constants\Extra;
use Gitter\Model\Blob;
use Gitter\Model\Commit\Commit;
use Gitter\Model\Commit\Diff;
use Gitter\Model\Tree;
use Gitter\Statistics\StatisticsInterface;
use ReflectionException;

class Repository
{
    protected string $path;
    protected Client $client;
    protected bool $commitsHaveBeenParsed = false;
    protected array $statistics = array();

    public function __construct($path, Client $client)
    {
        $this->setPath($path);
        $this->setClient($client);
    }

    /**
     * @param bool $value
     */
    public function setCommitsHaveBeenParsed(bool $value): void
    {
        $this->commitsHaveBeenParsed = $value;
    }

    /**
     * @return bool
     */
    public function getCommitsHaveBeenParsed(): bool
    {
        return $this->commitsHaveBeenParsed;
    }

    /**
     * Create a new git repository.
     */
    public function create($bare = null): self
    {
        mkdir($this->getPath());
        $command = Commands::INIT;

        if ($bare) {
            $command .= sprintf(" %s", Commands::BARE);
        }

        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Get a git configuration variable.
     *
     * @param string $key Configuration key
     */
    public function getConfig(string $key): string
    {
        $key = $this->getClient()->run($this, Commands::CONFIG . $key);

        return trim($key);
    }

    /**
     * Set a git configuration variable.
     *
     * @param string $key Configuration key
     * @param string $value Configuration value
     */
    public function setConfig(string $key, string $value): self
    {
        $command = sprintf("%s %s \"%s\"", Commands::CONFIG, $key, $value);
        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Add statistic aggregator.
     *
     * @param array|StatisticsInterface $statistics
     * @throws ReflectionException
     */
    public function addStatistics(StatisticsInterface|array $statistics): void
    {
        if (!is_array($statistics)) {
            $statistics = array($statistics);
        }

        foreach ($statistics as $statistic) {
            $reflect = new \ReflectionClass($statistic);
            $this->statistics[strtolower($reflect->getShortName())] = $statistic;
        }
    }

    /**
     * Get statistic aggregators.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        if (false === $this->getCommitsHaveBeenParsed()) {
            $this->getCommits();
        }

        foreach ($this->statistics as $statistic) {
            $statistic->sortCommits();
        }

        return $this->statistics;
    }

    /**
     * Add untracked files.
     *
     * @param array|string $files Files to be added to the repository
     */
    public function add(array|string $files = '.'): self
    {
        if (is_array($files)) {
            $files = implode(' ', array_map('escapeshellarg', $files));
        } else {
            $files = escapeshellarg($files);
        }

        $command = sprintf("%s %s", Commands::ADD, $files);
        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Add all untracked files.
     */
    public function addAll(): self
    {
        $command = sprintf("%s %s", Commands::ADD, "-A");
        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Commit changes to the repository.
     *
     * @param string $message Description of the changes made
     */
    public function commit(string $message): self
    {
        $command = sprintf("%s %s \"%s\"", Commands::COMMIT, "-m", $message);
        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Checkout a branch.
     *
     * @param string $branch Branch to be checked out
     */
    public function checkout(string $branch): self
    {
        $command = sprintf("%s %s", Commands::CHECKOUT, $branch);
        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Pull repository changes.
     */
    public function pull(): self
    {
        $this->getClient()->run($this, Commands::PULL);

        return $this;
    }

    /**
     * Update remote references.
     *
     * @param string|null $repository Repository to be pushed
     * @param string|null $refspec Ref-spec for the push
     */
    public function push(string $repository = null, string $refspec = null): self
    {
        $command = Commands::PUSH;

        if ($repository) {
            $command .= " $repository";
        }

        if ($refspec) {
            $command .= " $refspec";
        }

        $this->getClient()->run($this, $command);

        return $this;
    }

    /**
     * Get name of repository (top level directory).
     *
     * @return string
     */
    public function getName(): string
    {
        $name = rtrim($this->path, '/');

        if (strstr($name, DIRECTORY_SEPARATOR)) {
            $name = substr($name, strrpos($name, DIRECTORY_SEPARATOR) + 1);
        }

        return trim($name);
    }

    /**
     * Show a list of the repository branches.
     *
     * @return array List of branches
     */
    public function getBranches(): array
    {
        static $cache = array();

        if (array_key_exists($this->path, $cache)) {
            return $cache[$this->path];
        }

        $branches = $this->getClient()->run($this, Commands::BRANCH);
        $branches = explode("\n", $branches);
        $branches = array_filter(preg_replace('/[\*\s]/', '', $branches));

        if (empty($branches)) {
            return $cache[$this->path] = $branches;
        }

        // Since we've stripped whitespace, the result "* (detached from "
        // and "* (no branch)" that is displayed in detached HEAD state
        // becomes "(detachedfrom" and "(nobranch)" respectively.
        if (str_starts_with($branches[0], '(detachedfrom') || ('(nobranch)' === $branches[0])) {
            $branches = array_slice($branches, 1);
        }

        return $cache[$this->path] = $branches;
    }

    /**
     * Return the current repository branch.
     *
     * @return string|null current repository branch as a string, or NULL if in
     * detached HEAD state
     */
    public function getCurrentBranch(): ?string
    {
        $branches = $this->getClient()->run($this, Commands::BRANCH);
        $branches = explode("\n", $branches);

        foreach ($branches as $branch) {
            if ('*' === $branch[0]) {
                if (preg_match('/(detached|no branch)/', $branch)) {
                    return null;
                }

                return substr($branch, 2);
            }
        }

        return null;
    }

    /**
     * Check if a specified branch exists.
     *
     * @param string $branch Branch to be checked
     *
     * @return bool True if the branch exists
     */
    public function hasBranch(string $branch): bool
    {
        $branches = $this->getBranches();
        return in_array($branch, $branches);
    }

    /**
     * Create a new repository branch.
     *
     * @param string $branch Branch name
     */
    public function createBranch(string $branch): void
    {
        $command = sprintf("%s %s", Commands::BRANCH, $branch);
        $this->getClient()->run($this, $command);
    }

    /**
     * Create a new repository tag.
     *
     * @param string $tag Tag name
     */
    public function createTag(string $tag, $message = null): void
    {
        $command = Commands::TAG;

        if ($message) {
            $command .= sprintf(" %s %s '%s'", "-a", "-m", $message);
        }

        $command .= " $tag";

        $this->getClient()->run($this, $command);
    }

    /**
     * Show a list of the repository tags.
     *
     * @return array|null List of tags
     */
    public function getTags(): ?array
    {
        static $cache = array();

        if (array_key_exists($this->path, $cache)) {
            return $cache[$this->path];
        }

        $tags = $this->getClient()->run($this, Commands::TAG);
        $tags = explode("\n", $tags);
        array_pop($tags);

        if (empty($tags[0])) {
            return $cache[$this->path] = null;
        }

        return $cache[$this->path] = $tags;
    }

    /**
     * Show the amount of commits on the repository.
     *
     * @return string Total number of commits
     */
    public function getTotalCommits(string $file = null): string
    {
        if (defined(Extra::PHP_WINDOWS_VERSION_BUILD)) {
            $command = sprintf("rev-list --count --all %s", $file);
        } else {
            $command = sprintf("rev-list --all %s | wc -l", $file);
        }

        $commits = $this->getClient()->run($this, $command);

        return trim($commits);
    }

    /**
     * Show the repository commit log.
     *
     * @return array Commit log
     */
    public function getCommits(string $file = null): array
    {
        $commits = [];
        $command = 'log --pretty=format:"<item><hash>%H</hash><short_hash>%h</short_hash><tree>%T</tree><parents>%P</parents><author>%an</author><author_email>%ae</author_email><date>%at</date><commiter>%cn</commiter><commiter_email>%ce</commiter_email><commiter_date>%ct</commiter_date><message><![CDATA[%s]]></message></item>"';

        if ($file) {
            $command .= sprintf(" %s", $file);
        }

        $logs = $this->getPrettyFormat($command);

        foreach ($logs as $log) {
            $commit = new Commit();
            $commit->importData($log);
            $commits[] = $commit;

            foreach ($this->statistics as $statistic) {
                $statistic->addCommit($commit);
            }
        }

        $this->setCommitsHaveBeenParsed(true);

        return $commits;
    }

    /**
     * Show the data from a specific commit.
     *
     * @param string $commitHash Hash of the specific commit to read data
     *
     * @return Commit|array Commit data
     */
    public function getCommit(string $commitHash): Commit|array
    {
        if (version_compare($this->getClient()->getVersion(), Extra::V_1_8_4, '>=')) {
            $command = "show --ignore-blank-lines -w -b --pretty=format:\"<item><hash>%H</hash><short_hash>%h</short_hash><tree>%T</tree><parents>%P</parents><author>%an</author><author_email>%ae</author_email><date>%at</date><commiter>%cn</commiter><commiter_email>%ce</commiter_email><commiter_date>%ct</commiter_date><message><![CDATA[%s]]></message><body><![CDATA[%b]]></body></item>\" $commitHash";
            $logs = $this->getClient()->run($this, $command);
        } else {
            $logs = $this->getClient()->run(
                $this,
                "show --pretty=format:\"<item><hash>%H</hash><short_hash>%h</short_hash><tree>%T</tree><parents>%P</parents><author>%an</author><author_email>%ae</author_email><date>%at</date><commiter>%cn</commiter><commiter_email>%ce</commiter_email><commiter_date>%ct</commiter_date><message><![CDATA[%s]]></message><body><![CDATA[%b]]></body></item>\" $commitHash"
            );
        }

        $xmlEnd = strpos($logs, '</item>') + 7;
        $commitInfo = substr($logs, 0, $xmlEnd);
        $commitData = substr($logs, $xmlEnd);
        $logs = explode("\n", $commitData);
        array_shift($logs);

        // Read commit metadata
        $format = new PrettyFormat();
        $data = $format->parse($commitInfo);
        $commit = new Commit();
        $commit->importData($data[0]);

        if (empty($logs[1])) {
            $diffCommand = sprintf('%s %s ~1..%s', Commands::DIFF, $commitHash, $commitHash);
            $logs = explode("\n", $this->getClient()->run($this, $diffCommand));
        }

        $commit->setDiffs($this->readDiffLogs($logs));

        return $commit;
    }

    /**
     * Read diff logs and generate a collection of diffs.
     *
     * @param array $logs Array of log rows
     *
     * @return array       Array of diffs
     */
    public function readDiffLogs(array $logs): array
    {
        $diffs = array();
        $lineNumOld = 0;
        $lineNumNew = 0;
        foreach ($logs as $log) {
            if (str_starts_with($log, Commands::DIFF)) {
                if (isset($diff)) {
                    $diffs[] = $diff;
                }

                $diff = new Diff();
                if (preg_match('/^diff --[\S]+ a\/?(.+) b\/?/', $log, $name)) {
                    $diff->setFile($name[1]);
                }
                continue;
            }

            if (str_starts_with($log, Extra::INDEX)) {
                $diff->setIndex($log);
                continue;
            }

            if (str_starts_with($log, Extra::MINUS_SIGN)) {
                $diff->setOld($log);
                continue;
            }

            if (str_starts_with($log, Extra::PLUS_SIGN)) {
                $diff->setNew($log);
                continue;
            }

            // Handle binary files properly.
            if (str_starts_with($log, Extra::BINARY)) {
                $m = array();
                if (preg_match('/Binary files (.+) and (.+) differ/', $log, $m)) {
                    $diff->setOld($m[1]);
                    $diff->setNew("    {$m[2]}");
                }
            }

            if (!empty($log)) {
                switch ($log[0]) {
                    case Extra::AT_SIGN:
                        // Set the line numbers
                        preg_match('/@@ -([0-9]+)/', $log, $matches);
                        $lineNumOld = $matches[1] - 1;
                        $lineNumNew = $matches[1] - 1;
                        break;
                    case '-':
                        $lineNumOld++;
                        break;
                    case '+':
                        $lineNumNew++;
                        break;
                    default:
                        $lineNumOld++;
                        $lineNumNew++;
                }
            } else {
                $lineNumOld++;
                $lineNumNew++;
            }

            if ($diff) {
                $diff->addLine($log, $lineNumOld, $lineNumNew);
            }
        }

        if (isset($diff)) {
            $diffs[] = $diff;
        }

        return $diffs;
    }

    /**
     * Get the current HEAD.
     *
     * @param $default Optional branch to default to if in detached HEAD state.
     * If not passed, just grabs the first branch listed.
     *
     * @return string the name of the HEAD branch, or a backup option if
     * in detached HEAD state
     */
    public function getHead($default = null)
    {
        $file = '';
        if (file_exists($this->getPath() . Extra::GIT_HEAD)) {
            $file = file_get_contents($this->getPath() . Extra::GIT_HEAD);
        } elseif (file_exists($this->getPath() . Extra::HEAD)) {
            $file = file_get_contents($this->getPath() . Extra::HEAD);
        }

        // Find first existing branch
        foreach (explode("\n", $file) as $line) {
            $m = array();
            if (preg_match('#ref:\srefs/heads/(.+)#', $line, $m)) {
                if ($this->hasBranch($m[1])) {
                    return $m[1];
                }
            }
        }

        // If we were given a default branch, and it exists, return that.
        if (null !== $default && $this->hasBranch($default)) {
            return $default;
        }

        // Otherwise, return the first existing branch.
        $branches = $this->getBranches();
        if (!empty($branches)) {
            return current($branches);
        }

        // No branches exist - null is the best we can do in this case.
        return null;
    }

    /**
     * Extract the tree hash for a given branch or tree reference.
     *
     * @param string $branch
     *
     * @return bool|string
     */
    public function getBranchTree(string $branch): bool|string
    {
        $command = sprintf("log --pretty=\"%s\" --max-count=1 %s", "%T", $branch);
        $hash = $this->getClient()->run($this, $command);
        $hash = trim($hash, "\r\n ");

        return $hash ?: false;
    }

    /**
     * Get the Tree for the provided folder.
     *
     * @param string $tree Folder that will be parsed
     *
     * @return Tree   Instance of Tree for the provided folder
     */
    public function getTree(string $tree): Tree
    {
        $tree = new Tree($tree, $this);
        $tree->parse();

        return $tree;
    }

    /**
     * Get the Blob for the provided file.
     *
     * @param string $blob File that will be parsed
     *
     * @return Blob   Instance of Blob for the provided file
     */
    public function getBlob(string $blob): Blob
    {
        return new Blob($blob, $this);
    }

    /**
     * Blames the provided file and parses the output.
     *
     * @param string $file File that will be blamed
     *
     * @return array  Commits hashes containing the lines
     */
    public function getBlame(string $file): array
    {
        $blame = array();
        $command = sprintf("%s -s %s", Commands::BLAME, $file);
        $logs = $this->getClient()->run($this, $command);
        $logs = explode("\n", $logs);

        $i = 0;
        $previousCommit = '';
        foreach ($logs as $log) {
            if ('' == $log) {
                continue;
            }

            preg_match_all("/([a-zA-Z0-9^]{8})\s+.*?([0-9]+)\)(.+)/", $log, $match);

            $currentCommit = $match[1][0];
            if ($currentCommit != $previousCommit) {
                $i++;
                $blame[$i] = array('line' => '', 'commit' => $currentCommit);
            }

            $blame[$i]['line'] .= PHP_EOL . $match[3][0];
            $previousCommit = $currentCommit;
        }

        return $blame;
    }

    /**
     * Get the current Repository path.
     *
     * @return string Path where the repository is located
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the current Repository path.
     *
     * @param string $path Path where the repository is located
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Get the current Client instance.
     *
     * @return Client Client instance
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Set the Client.
     *
     * @param Client $client
     * @return Repository
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get and parse the output of a git command with a XML-based pretty format.
     *
     * @param string $command Command to be run by git
     *
     * @return array  Parsed command output
     */
    public function getPrettyFormat(string $command): array
    {
        $output = $this->getClient()->run($this, $command);
        $format = new PrettyFormat();

        return $format->parse($output);
    }
}
