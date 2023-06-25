<?php

namespace Gitter\Constants;

abstract class Commands
{
    public const GIT = 'git';
    public const GIT_PATH = '/usr/bin/git';
    public const INIT = 'init';
    public const BARE = '--bare';
    public const CONFIG = 'config ';
    public const ADD = 'add';
    public const COMMIT = 'commit';
    public const CHECKOUT = 'checkout';
    public const PULL = 'pull';
    public const PUSH = 'push';
    public const BRANCH = 'branch';
    public const TAG = 'tag';
    public const DIFF = 'diff';
    public const BLAME = 'blame';
    public const VERSION = '--version';
    public const LOG = 'log';
    public const LOG_ONELINE = 'log --oneline';
}
