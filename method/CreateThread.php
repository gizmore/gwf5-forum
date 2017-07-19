<?php
/**
 * Start a new thread.
 * @author gizmore
 * @see GWF_ForumBoard
 * @see GWF_ForumThread
 * @see GWF_ForumPost
 */
final class Forum_CreateThread extends GWF_MethodForm
{
    private $post;
    
    public function isUserRequired() { return true; }
    
    public function isGuestAllowed() { return Module_Forum::instance()->cfgGuestPosts(); }
    
    public function createForm(GWF_Form $form)
    {
        $gdo = GWF_ForumThread::table();
        $posts = GWF_ForumPost::table();
        $form->addFields(array(
            $gdo->gdoColumn('thread_board')->initial(Common::getRequestString('board'))->writable(false),
            $gdo->gdoColumn('thread_title'),
            $posts->gdoColumn('post_message'),
            $posts->gdoColumn('post_attachment'),
            GDO_Submit::make(),
            GDO_AntiCSRF::make(),
        ));
        
        $module = Module_Forum::instance();
        $user = GWF_User::current();
        if (!$module->canUpload($user))
        {
            $form->removeField('post_attachment');
        }
    }
    
    public function formValidated(GWF_Form $form)
    {
        $thread = GWF_ForumThread::blank($form->values())->insert();
        $post = $this->post = GWF_ForumPost::blank($form->values())->setVar('post_thread', $thread->getID())->insert();
        Module_Forum::instance()->saveConfigVar('forum_latest_post_date', $post->getCreated());
        GWF_ForumRead::markRead(GWF_User::current(), $post);
        $redirect = GWF_Website::redirectMessage(href('Forum', 'Thread', '&thread='.$thread->getID()));
        return $this->message('msg_thread_created')->add($redirect);
    }
    
    public function afterExecute()
    {
        if ($this->getForm()->validated)
        {
            GWF_Hook::call('ForumPostCreated', $this->post);
        }
    }
}
