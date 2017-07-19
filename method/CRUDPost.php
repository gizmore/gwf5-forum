<?php
final class Forum_CRUDPost extends GWF_MethodCrud
{
    public function gdoTable() { return GWF_ForumPost::table(); }
    public function hrefList() { return href('Forum', 'Thread', '&thread='.$this->thread->getID()); }
   
    public function isGuestAllowed() { return Module_Forum::instance()->cfgGuestPosts(); }
    
    public function canCreate(GDO $gdo) { return true; }
    public function canUpdate(GDO $gdo) { return $gdo->canEdit(GWF_User::current()); }
    public function canDelete(GDO $gdo) { return GWF_User::current()->isAdmin(); }
    
    private $thread;
    
    public function execute()
    {
        # 1. Get thread
        $user = GWF_User::current();
        if ( ($pid = Common::getGetString('quote')) ||
             ($pid = Common::getGetString('id')) )
        {
            $post = GWF_ForumPost::table()->find($pid);
            $this->thread = $post->getThread();
        }
        elseif ($tid = Common::getGetString('reply'))
        {
            $this->thread = GWF_ForumThread::table()->find($tid);
        }
        else
        {
            return $this->error('err_thread');
        }
        #
        $_REQUEST['board'] = $this->thread->getBoardID();
        
        
        # 2. Check permission
        if (!$this->thread->canView($user))
        {
            return $this->error('err_permission');
        }
        if ($this->thread->isLocked())
        {
            return $this->error('err_thread_locked');
        }

        # 3. Execute
        $response = parent::execute();
        $tabs = Module_Forum::instance()->renderTabs();
        return $tabs->add($response);
    }
    
    public function createForm(GWF_Form $form)
    {
        $gdo = $this->gdoTable();
        $boardId = Common::getRequestString('board');
        $form->addFields(array(
            GDO_Hidden::make('post_thread')->initial($this->thread->getID()),
            $gdo->gdoColumn('post_message'),
            $gdo->gdoColumn('post_attachment'),
        ));
        $this->createFormButtons($form);
    }
    
    public function afterCreate(GWF_Form $form)
    {
        $module = Module_Forum::instance();
        $module->saveConfigVar('forum_latest_post_date', $this->gdo->getCreated());
        GWF_UserSetting::inc('forum_posts');
        GWF_ForumRead::markRead(GWF_User::current(), $this->gdo);
    }
    
    public function afterExecute()
    {
        if ($this->crudMode === self::CREATED)
        {
            GWF_Hook::call('ForumPostCreated', $this->gdo);
        }
    }
}
