<?php
namespace Gitter\Statistics;

use Gitter\Util\Collection;
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
        $email      = $commit->getAuthor()->getEmail();
        $commitDate = $commit->getCommiterDate()->format('Y-m-d');

        $this->items[$email][$commitDate][] = $commit;
    }
}