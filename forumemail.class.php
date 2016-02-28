<?php
if(!defined('IN_DISCUZ')){
    exit('Access Denied');
}

class plugin_game_mail{
    function post_game_mail(){
        register_shutdown_function('post_game_mail_end_func');
    }
}

class plugin_game_mail_forum extends plugin_game_mail {
}

function post_game_mail_end_func(){
    global $_G, $pid, $tid, $subject, $message;
    $set = $_G['cache']['plugin']['game_mail'];
    $newthread_isopen = $set['newthread_isopen'];
    $reply_isopen = $set['reply_isopen'];
    $time_now = dgmdate($_G['timestamp'], 'Y-m-d H:i:s');

    if(!$newthread_isopen && !$reply_isopen) return;
    if(!$pid) return; 
    if(!in_array($_G['gp_action'],array('reply','newthread'))) return; 
    if($_G['gp_comment']) return; 
    $fid = $_G['gp_fid'];if (!$fid) return; 
    
#------
    $rnlist = array();
    $query = DB::query("SELECT a.uid,a.username,b.realname FROM ".DB::table('common_member')." as a,".DB::table('common_member_profile')." as b where a.uid=
b.uid");
    while ($value = DB::fetch($query)) {
        $rnlist[$value['uid']] = $value['realname'];
    }

    $query = DB::query("SELECT uid,username,email FROM ".DB::table('common_member')." where email <> ''");
    while($row = DB::fetch($query)) {
        $emaillist[$row[uid]] = $row[email];
        $userlist[$row[uid]] = empty($rnlist[$row[uid]])?$row[username]:$rnlist[$row[uid]];
    }

    $query = DB::query("SELECT uid,newthreademail FROM ".DB::table('cdb_dddie_member_forumfield_home'));
    while($row = DB::fetch($query)) {
        $newthreadEmail[] = array( 'uid' => $row[uid],
                           'username' => $userlist[$row[uid]],
                           'email' => $emaillist[$row[uid]],
                           'set' => (array)unserialize($row[newthreademail]),
                         );
    }
    if(!function_exists('sendmail')) {
        include libfile('function/mail');
    }
if($_G['gp_action'] == "newthread" && $newthread_isopen){
    foreach($newthreadEmail as $id => $info){
        if($info['set'][$fid] == 1){
            if($emailto == ""){
                $emailto = $info['username']." <".$info['email'].">";
            }else{
                $emailto = $emailto.",".$info['username']." <".$info['email'].">";
            }
        }
    }
    $url = $_G[siteurl]."forum.php?mod=viewthread&tid=$tid";
    $message = $userlist[$_G[uid]]."于".$time_now."发表了新主题<a href='".$url."'>".$subject."</a> <a href='".$url."'>点击查看</a>";
    $mail_subject = "[发布帖子通知]主题:".$subject;
    sendmail("$emailto","$mail_subject","$message");
    file_put_contents("/tmp/aaaaa",$message);
}elseif($_G['gp_action'] == "reply" && $reply_isopen){
    $query = DB::query("SELECT authorid,subject FROM ".DB::table('forum_thread')." WHERE tid='$_G[tid]'");
    while($row = DB::fetch($query)) {
        $authid=$row[authorid];
        $subject=$row[subject];
    }
    $posttable = getposttablebytid($_G['tid']);
    #$query = DB::query("SELECT distinct(authorid) FROM ".DB::table($posttable)." WHERE tid='$_G[tid]' and authorid <> '$_G[uid]'");
    $query = DB::query("SELECT distinct(authorid) FROM ".DB::table($posttable)." WHERE tid='$_G[tid]'");
    while($row = DB::fetch($query)) {
        if($emailto == ""){
            $emailto = $userlist[$row['authorid']]." <".$emaillist[$row['authorid']].">";
        }else{
            $emailto = $emailto.",".$userlist[$row['authorid']]." <".$emaillist[$row['authorid']].">";
        }
    }
    $thapost = DB::fetch_first("SELECT tid, author, authorid, useip, dateline, anonymous, status, message FROM ".DB::table($posttable)." WHERE pid='$pid' AND (invisible='0' OR (authorid='$_G[uid]' AND invisible='-2'))");
    $url = $_G[siteurl]."forum.php?mod=redirect&goto=findpost&pid=$pid&ptid=$thapost[tid]";
    $message = $userlist[$_G[uid]]."于".$time_now."回复了".$userlist[$authid]."发布的帖子:<a href='".$url."'>".$subject."</a> <a href='".$url."'>查看</a>";
    $mail_subject = "[回复帖子通知]主题:".$subject;
    sendmail("$emailto","$mail_subject","$message");
    file_put_contents("/tmp/aaaaa",$emailto);
}
    $timest = dgmdate($_G['timestamp'], 'Y-m-d H:i:s');
#------
}

?>
