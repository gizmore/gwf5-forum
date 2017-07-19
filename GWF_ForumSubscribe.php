<?php
final class GWF_ForumSubscribe extends GDO
{
    public function gdoCached() { return false; }
    public function gdoColumns()
    {
        return array(
            GDO_User::make('subscribe_user')->primary(),
            GDO_ForumBoard::make('subscribe_board')->primary(),
            GDO_Object::make('subscribe_thread')->table(GWF_ForumThread::table())->primary(),
        );
    }
    
}
