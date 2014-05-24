<?php
namespace Gitter\Model\Commit;


class Collection implements \ArrayAccess {
    /**
     * @var array
     */
    protected $commits;

    /**
     * @return array
     */
    public function getCommits()
    {
        return $this->commits;
    }

    /**
     * @param array $commits
     */
    public function setCommits($commits)
    {
        $this->commits = $commits;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->commits[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->commits[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->commits[] = $value;
        } else {
            $this->commits[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->commits[$offset]);
    }
}