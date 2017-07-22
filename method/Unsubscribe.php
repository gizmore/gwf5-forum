<?php
final class Forum_Unsubscribe extends GWF_Method
{
    public function execute()
    {
        $user = GWF_User::current();
        $uid = $user->getID();
        if ($boardId = Common::getRequestInt('board'))
        {
            if ($boardId === 1)
            {
                return $this->error('err_please_use_subscribe_all');
            }
            $board = GWF_ForumBoard::findById($boardId);
            GWF_ForumBoardSubscribe::table()->deleteWhere("subscribe_user=$uid AND subscribe_board=$boardId")->exec();
            $user->tempUnset('gwf_forum_board_subsciptions');
            $user->recache();
            $redirect = GWF_Website::redirectMessage(href('Forum', 'Boards', '&board='.$board->getParent()->getID()));
        }
        elseif ($threadId = Common::getRequestInt('thread'))
        {
            $thread = GWF_ForumThread::findById($threadId);
            GWF_ForumThreadSubscribe::table()->deleteWhere("subscribe_user=$uid AND subscribe_thread=$threadId")->exec();
            $user->tempUnset('gwf_forum_thread_subsciptions');
            $user->recache();
            $redirect = GWF_Website::redirectMessage(href('Forum', 'Boards', '&boardid='.$thread->getBoard()->getID()));
        }
        
        return $this->message('msg_unsubscribed')->add($redirect);
    }
}
