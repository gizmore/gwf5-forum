<?php
final class GWF_ForumThreadSubscribe extends GDO
{
    public function gdoCached() { return false; }
    public function gdoColumns()
    {
        return array(
            GDO_User::make('subscribe_user')->primary(),
            GDO_Object::make('subscribe_thread')->table(GWF_ForumThread::table())->primary(),
        );
    }
    
    /**
     * @return GWF_User
     */
    public function getUser() { return $this->getValue('subscribe_user'); }
    public function getUserID() { return $this->getVar('subscribe_user'); }
    
    public function gdoAfterCreate()
    {
        $this->getUser()->tempUnset('gwf_forum_board_subsciptions');
    }
}
