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
use Gitter\Constants\Exceptions;
use Gitter\Constants\Extra;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Client
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @param string|null $path
     */
    public function __construct(string $path = null)
    {
        if (!$path) {
            $finder = new ExecutableFinder();
            $path = $finder->find(Commands::GIT, Commands::GIT_PATH);
        }

        $this->setPath($path);
    }

    /**
     * Creates a new repository on the specified path.
     *
     * @param string $path Path where the new repository will be created
     *
     * @return Repository Instance of Repository
     */
    public function createRepository(string $path, $bare = null): Repository
    {
        if (file_exists($path . Extra::GIT_HEAD) && !file_exists($path . Extra::HEAD)) {
            $exceptionMessage = sprintf("%s %s", Exceptions::GIT_REPOSITORY_EXISTS, $path);
            throw new \RuntimeException($exceptionMessage);
        }

        $repository = new Repository($path, $this);

        return $repository->create($bare);
    }

    /**
     * Opens a repository at the specified path.
     *
     * @param string $path Path where the repository is located
     *
     * @return Repository Instance of Repository
     */
    public function getRepository(string $path): Repository
    {
        if (!file_exists($path) || !file_exists($path . Extra::GIT_HEAD) && !file_exists($path . Extra::HEAD)) {
            $exceptionMessage = sprintf("%s %s", Exceptions::NO_GIT_REPOSITORY, $path);
            throw new \RuntimeException($exceptionMessage);
        }

        return new Repository($path, $this);
    }

    /**
     * @param $repository
     * @param string $command
     * @return string
     */
    public function run($repository, string $command): string
    {
        if (version_compare($this->getVersion(), Extra::V_1_7_2, '>=')) {
            $command = sprintf('-c "color.ui"=false %s', $command);
        }

        $commandLine = sprintf("%s %s", $this->getPath(), $command);
        $process = new Process($commandLine, $repository->getPath());
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * @return mixed|string
     */
    public function getVersion(): mixed
    {
        static $version;

        if (null !== $version) {
            return $version;
        }

        $commandLine = sprintf("%s %s",$this->getPath(), Commands::VERSION);
        $process = new Process($commandLine);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $version = trim(substr($process->getOutput(), 12));

        return $version;
    }

    /**
     * Get the current Git binary path.
     *
     * @return string Path where the Git binary is located
     */
    protected function getPath(): string
    {
        return escapeshellarg($this->path);
    }

    /**
     * Set the current Git binary path.
     *
     * @param string $path Path where the Git binary is located
     */
    protected function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
