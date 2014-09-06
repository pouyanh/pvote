<?php
/**
 * Created by IntelliJ IDEA.
 * User: pouyan
 * Date: 8/25/14
 * Time: 4:29 AM
 */

namespace PVote\Base;

use \Phalcon\Mvc\Controller as PhController;

class Controller extends PhController
{
    protected function hasAccount()
    {
        $user = $this->userSecurity->getCurrentUser();

        return !is_null($user);
    }
}
