<?php

/**
 *      [dddie!] (C)2013-2066 dddie.com Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: plugin_game_mail.php 2013-06-15 18:08:00 ÔÆ·É $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$set= $_G['cache']['plugin']['game_mail'];
$forumallowlist = (array)unserialize($set['forum_offset']);

#---------
function forumselect() {
	global $_G,$forumallowlist;

	if(!isset($_G['cache']['forums'])) {
		loadcache('forums');
	}
	$forumcache = &$_G['cache']['forums'];
	foreach($forumcache as $forum) {
		if((!$forum['status'] || $forum['hidemenu']) && !$showhide) {
			continue;
		}
		if($forum['type'] == 'group') {
            $alist[$forum['fid']] = array(
                'fid' => $forum['fid'],
                'type' => $forum['type'],
                'name' => $forum['name'],
            );
			$visible[$forum['fid']] = true;
		} elseif($forum['type'] == 'forum' && in_array($forum['fid'], $forumallowlist) && isset($visible[$forum['fup']]) && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$_G[uid]\t")) && (!$special || (substr($forum['allowpostspecial'], -$special, 1)))) {
            $alist[$forum['fid']] = array(
                'fid' => $forum['fid'],
                'type' => $forum['type'],
                'name' => $forum['name'],
            );
			$visible[$forum['fid']] = true;
		} elseif($forum['type'] == 'sub' && in_array($forum['fid'], $forumallowlist) && isset($visible[$forum['fup']]) && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$_G[uid]\t")) && (!$special || substr($forum['allowpostspecial'], -$special, 1))) {
            $alist[$forum['fid']] = array(
                'fid' => $forum['fid'],
                'type' => $forum['type'],
                'name' => $forum['name'],
            );
		}
	}
	return $alist;
}
#---------
$forumlist=forumselect();

$_GET['op'] = empty($_GET['op']) ? '' : trim($_GET['op']);

#if(empty($_G['setting']['sendmailday'])) {
#	showmessage('no_privilege_sendmailday');
#}

if(submitcheck('setsendemailsubmit')) {
	$_G['gp_sendmail'] = addslashes(serialize($_G['gp_sendmail']));
	#DB::update('cdb_dddie_member_forumfield_home', array('newthreademail' => $_G['gp_sendmail']), array('uid' => $_G['uid']));
    DB::query("REPLACE INTO ".DB::table('cdb_dddie_member_forumfield_home')." (uid,newthreademail) VALUES ('$_G[uid]', '$_G[gp_sendmail]')");
	showmessage('do_success', 'home.php?mod=spacecp&ac=plugin&id=game_mail:forumemail');
}


if(empty($space['email']) || !isemail($space['email'])) {
	showmessage('email_input');
}
$space['newthreademail'] = DB::result_first("SELECT newthreademail FROM ".DB::table('cdb_dddie_member_forumfield_home')." where uid=".$_G['uid']."");
$space['newthreademail'] = (array)unserialize($space['newthreademail']);
$sendmail = array();
if($space['newthreademail'] && is_array($space['newthreademail'])) {
	foreach($space['newthreademail'] as $mkey=>$mailset) {
		if($mkey != 'frequency') {
			$sendmail[$mkey] = empty($space['newthreademail'][$mkey]) ? '' : ' checked';
		} else {
			$sendmail[$mkey] = array($space['newthreademail']['frequency'] => 'selected');
		}
	}
}

#include_once template("game_mail:forumemail");

?>
