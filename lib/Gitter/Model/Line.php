<?php

/*
 * This file is part of the Gitter library.
 *
 * (c) Klaus Silveira <klaussilveira@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitter\Model;

class Line extends AbstractModel
{
    protected $line;
    protected $type;

    public function __construct($data)
    {
        if (!empty($data)) {
            switch ($data[0]) {
                case '@':
                    $this->setType('chunk');
                    break;
                case '-':
                    $this->setType('old');
                    break;
                case '+':
                    $this->setType('new');
                    break;
            }
        }

        $this->setLine($data);
    }

    public function getLine()
    {
        return $this->line;
    }

    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
