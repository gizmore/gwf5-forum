<?php
final class Forum_CronjobMailer extends GWF_MethodCronjob
{
    public function run()
    {
        $module = Module_Forum::instance();
        $lastId = $module->cfgLastPostMail();
        $post = true;
        while ($post)
        {
            if ($post = GWF_ForumPost::table()->select()->where("post_id > $lastId")->order('post_id')->first()->exec()->fetchObject())
            {
                $this->mailSubscriptions($module, $post);
                $lastId = $post->getID();
                $module->saveConfigVar('forum_mail_sent_for_post', $lastId);
            }
        }
    }
    
    private function mailSubscriptions(Module_Forum $module, GWF_ForumPost $post)
    {
        $this->logNotice(sprintf("Sending mails for {$post->getThread()->getTitle()}"));
        $mid = $module->getID();
        $sentTo = [];
        
        # Sent to those who subscribe the whole board
        $query = GWF_UserSetting::table()->select('gwf_user.*')->joinObject('uset_user');
        $query->where("uset_name='forum_subscription'")->where("uset_value='fsub_all'");
        $result = $query->fetchTable(GWF_User::table())->uncached()->exec();
        while ($user = $result->fetchObject())
        {
            if (!in_array($user->getID(), $sentTo, true))
            {
                $this->mailSubscription($post, $user);
                $sentTo[] = $user->getID();
            }
        }
        
        # Sent to those who subscribe their own threads
        $query = GWF_ForumPost::table()->select('gwf_user.*')->joinObject('post_creator');
        $query->join("LEFT JOIN gwf_usersetting ON uset_user=user_id AND uset_name='forum_subscription'");
        $query->where("post_thread={$post->getThreadID()}")->where("uset_value IS NULL OR uset_value = 'fsub_own'");
        $result = $query->fetchTable(GWF_User::table())->uncached()->exec();
        while ($user = $result->fetchObject())
        {
            if (!in_array($user->getID(), $sentTo, true))
            {
                $this->mailSubscription($post, $user);
                $sentTo[] = $user->getID();
            }
        }
        
        # Sent to those who subscribed via thread or board
        $bids = implode(',', $this->getBoardIDs($post));
        $query = GWF_ForumSubscribe::table()->select('gwf_user.*')->joinObject('subscribe_user');
        $query->where("subscribe_thread={$post->getThreadID()}")->or("subscribe_board IN ($bids)");
        $result = $query->fetchTable(GWF_User::table())->uncached()->exec();
        while ($user = $result->fetchObject())
        {
            if (!in_array($user->getID(), $sentTo, true))
            {
                $this->mailSubscription($post, $user);
                $sentTo[] = $user->getID();
            }
        }
    
    }
    
    private function getBoardIDs(GWF_ForumPost $post)
    {
        $ids = [];
        $board = $post->getThread()->getBoard();
        while ($board)
        {
            $ids[] = $board->getID();
            $board = $board->getParent();
        }
        return $ids;
    }
    
    private function mailSubscription(GWF_ForumPost $post, GWF_User $user)
    {
        $mail = GWF_Mail::botMail();
        $thread = $post->getThread();
        $sitename = $this->getSiteName();
        $username = $user->displayNameLabel();
        $poster = $post->getCreator()->displayNameLabel();
        $title = $thread->displayTitle();
        $message = $post->displayMessage();
        $linkUnsub = GWF_HTML::anchor(url('Forum', 'UnsubscribeAll', '&token='.$user->gdoHashcode()));
        $args = [$username, $sitename, $title, $poster, $message, $linkUnsub];
        $mail->setSubject(tusr($user, 'mail_subj_forum_post', [$sitename, $title]));
        $mail->setBody(tusr($user, 'mail_body_forum_post', $args));
        $mail->sendToUser($user);
    }
}
