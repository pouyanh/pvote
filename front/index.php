<?php
/**
 * Created by IntelliJ IDEA.
 * User: pouyan
 * Date: 8/25/14
 * Time: 2:51 AM
 */

require '../vendor/autoload.php';

putenv('ROOT_PATH=' . __DIR__ . '/..');

echo (new \PVote\Bootstrap())->render();
