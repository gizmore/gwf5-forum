<?php
/**
 * Display a forum thread.
 * @author gizmore
 */
final class Forum_Thread extends GWF_Method
{
    public function execute()
    {
        $thread = GWF_ForumThread::table()->find(Common::getRequestString('thread'));
        $_REQUEST['board'] = $thread->getBoardID();
        $tabs = Module_Forum::instance()->renderTabs();
        return $tabs->add($this->templatePHP('thread.php', ['thread' => $thread]));
    }
}
