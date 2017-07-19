<?php
final class Forum_DownloadAttachment extends GWF_Method
{
    public function execute()
    {
        $user = GWF_User::current();
        $table = GWF_ForumPost::table();
        $post = $table->find(Common::getGetString('post'));
        if (!$post->canView($user))
        {
            return $this->error('err_permission');
        }
        return $this->dowloadAttachment($post, method('GWF', 'GetFile'));
    }
    
    private function dowloadAttachment(GWF_ForumPost $post, GWF_GetFile $method)
    {
        return $method->executeWithId($post->getAttachmentID());
    }
}
