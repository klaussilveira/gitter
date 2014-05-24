<?php
namespace Gitter\Statistics;

use Gitter\Model\Commit\Collection;
use Gitter\Model\Commit\Commit;

/**
 * Aggregate statistics based on contributor
 */
class Contributors extends Collection implements StatisticsInterface
{
    /**
     * @param Commit $commit
     */
    public function addCommit(Commit $commit)
    {
        $email = $commit->getAuthor()->getEmail();
        $name  = $commit->getAuthor()->getName();

        $commitDate  = $commit->getCommiterDate()->format('Y-m-d');

        if (!isset($this->commits[$email]['name'])) {
            $this->commits[$email]['name'] = $name;
        }

        $this->commits[$email]['commits'][$commitDate][] = $commit;
    }
}