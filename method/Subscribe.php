<?php
final class Forum_Subscribe extends GWF_Method
{
    public function execute()
    {
        $user = GWF_User::current();
        if ($boardId = Common::getRequestString('board'))
        {
            if ($boardId === '1')
            {
                return $this->error('err_please_use_subscribe_all');
            }
            $board = GWF_ForumBoard::findById($boardId);
            GWF_ForumBoardSubscribe::blank(array(
                'subscribe_user' => $user->getID(),
                'subscribe_board' => $boardId,
            ))->replace();
            $user->tempUnset('gwf_forum_board_subsciptions');
            $user->recache();
            $redirect = GWF_Website::redirectMessage(href('Forum', 'Boards', '&board='.$board->getParent()->getID()));
        }
        elseif ($threadId = Common::getRequestString('thread'))
        {
            $thread = GWF_ForumThread::findById($threadId);
            GWF_ForumThreadSubscribe::blank(array(
                'subscribe_user' => $user->getID(),
                'subscribe_thread' => $threadId,
            ))->replace();
            $user->tempUnset('gwf_forum_thread_subsciptions');
            $user->recache();
            $redirect = GWF_Website::redirectMessage(href('Forum', 'Boards', '&boardid='.$thread->getBoard()->getID()));
        }
        
        return $this->message('msg_subscribed')->add($redirect);
    }
}
