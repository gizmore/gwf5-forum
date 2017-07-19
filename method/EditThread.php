<?php
/**
 * Start a new thread.
 * @author gizmore
 * @see GWF_ForumBoard
 * @see GWF_ForumThread
 * @see GWF_ForumPost
 */
final class Forum_EditThread extends GWF_MethodForm
{
    /**
     * @var GWF_ForumThread
     */
    private $thread;
    
    public function isUserRequired() { return true; }
    public function isGuestAllowed() { return Module_Forum::instance()->cfgGuestPosts(); }
    
    public function execute()
    {
        $this->thread = GWF_ForumThread::table()->find(Common::getGetString('id'));
        
        $response = parent::execute();
        $tabs = Module_Forum::instance()->renderTabs();
        return $tabs->add($response);
    }
    
    public function createForm(GWF_Form $form)
    {
        $user = GWF_User::current();
        $gdo = $this->thread;
        if ($user->isStaff())
        {
            $form->addField($gdo->gdoColumn('thread_board'));
        }
        $form->addFields(array(
            $gdo->gdoColumn('thread_title'),
            GDO_Submit::make(),
            GDO_Submit::make('delete'),
            GDO_AntiCSRF::make(),
        ));
        $form->withGDOValuesFrom($gdo);
    }
    
    public function formValidated(GWF_Form $form)
    {
        $response = null;
        $this->thread->saveVar('thread_title', $form->getVar('thread_title'));
        if ($form->hasChanged('thread_board'))
        {
            $response = $this->changeBoard($form->getValue('thread_board'));
        }
        $redirect = GWF_Website::redirectMessage(href('Forum', 'Thread', '&thread='.$this->thread->getID()));
        return $this->message('msg_thread_edited')->add($response)->add($redirect);
    }
    
    private function changeBoard(GWF_ForumBoard $newBoard)
    {
        $postsBy = $this->thread->getPostCount();
        $oldBoard = $this->thread->getBoard();
        GWF_Log::logDebug(sprintf('EditThread::changeBoard(%s => %s)', $oldBoard->getID(), $newBoard->getID()));
        $oldBoard->increaseCounters(-1, -$postsBy);
        $newBoard->increaseCounters(1, $postsBy);
        $this->thread->saveVar('thread_board', $newBoard->getID());
        return $this->message('msg_thread_moved');
    }
        
    
    
    
}
