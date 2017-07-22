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
            $redirect = GWF_Website::redirectMessage(href('Forum', 'Boards', '&board='.$board->getParent()->getID()));
        }
        elseif ($threadId = Common::getRequestString('thread'))
        {
            $thread = GWF_ForumThread::findById($threadId);
            $boardId = null;
            $redirect = GWF_Website::redirectMessage(href('Forum', 'Boards', '&boardid='.$thread->getBoard()->getID()));
        }
        
        return $this->message('msg_subscribed')->add($redirect);
    }
}
