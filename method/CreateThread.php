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
    }
    
    public function formValidated(GWF_Form $form)
    {
        $thread = GWF_ForumThread::blank($form->values())->insert();
        $post = GWF_ForumPost::blank($form->values())->setVar('post_thread', $thread->getID())->insert();
        $redirect = GWF_Website::redirectMessage(href('Forum', 'Thread', '&thread='.$thread->getID()));
        return $this->message('msg_thread_created')->add($redirect);
    }
}
