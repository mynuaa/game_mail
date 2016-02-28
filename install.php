<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS cdb_dddie_member_forumfield_home;
CREATE TABLE cdb_dddie_member_forumfield_home (
  `uid` mediumint(8) unsigned NOT NULL,
  `newthreademail` text NOT NULL,
  PRIMARY KEY  (`uid`)
) ;

EOF;

runquery($sql);

$finish = TRUE;

?>
