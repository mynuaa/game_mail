<?php

/**
 *      [Discuz!] (C)2013-2066 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: uninstall.php 6752 2013-06-15 18:08  ÔÆ·É $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS cdb_dddie_member_forumfield_home;

EOF;

runquery($sql);

$finish = TRUE;
