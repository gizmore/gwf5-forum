<?php
final class GWF_ForumRead extends GDO
{
    ###########
    ### GDO ###
    ###########
    public function gdoEngine() { return self::MYISAM; } # Faster inserts
    public function gdoCached() { return false; } # No L1/L2 cache
    public function gdoColumns()
    {
        return array(
            GDO_User::make('read_user')->primary(),
            GDO_Object::make('read_post')->table(GWF_ForumPost::table())->primary(),
        );
    }
    
    ################
    ### MarkRead ###
    ################
    public static function markRead(GWF_User $user, GWF_ForumPost $post)
    {
        return self::blank(['read_user'=>$user->getID(), 'read_post'=>$post->getID()])->replace();
    }
    
    
    ####################
    ### Unread Query ###
    ####################
    public static function countUnread(GWF_User $user)
    {
        $unread = self::getUnread($user);
        $count = count($unread);
        if ($count == 0)
        {
            # We have 0 unread posts.
            $module = Module_Forum::instance();
            $latest = $module->cfgLastPostDate();
            $latestU = GWF_UserSetting::userGet($user, 'forum_readmark')->getValue();
            if ($latest !== $latestU)
            {
                # We have read all and can move the marker to current timestamp.
                GWF_UserSetting::userSet($user, 'forum_readmark', $latest);
                GWF_ForumRead::table()->deleteWhere("read_user={$user->getID()}");
            }
        }
        return $count;
    }
    
    public static function getUnreadBoards(GWF_User $user) { return self::getUnreadSection($user, 0); }
    public static function getUnreadThreads(GWF_User $user) { return self::getUnreadSection($user, 1); }
    public static function getUnreadPosts(GWF_User $user) { return self::getUnreadSection($user, 2); }
    public static function getUnreadSection(GWF_User $user, int $section)
    {
        $back = [];
        foreach (self::getUnread($user) as $data)
        {
            $back[$data[$section]] = true;
        }
        return $back;
    }
    
    public static function getUnread(GWF_User $user)
    {
        static $cache; # Cache only in this request
        if (!isset($cache))
        {
            $cache = self::queryUnread($user);
        }
        return $cache;
    }
    
    private static function queryUnread(GWF_User $user)
    {
        $module = Module_Forum::instance();
        $latest = $module->cfgLastPostDate();
        $latestU = GWF_UserSetting::userGet($user, 'forum_readmark')->getValue();
        if ( ($latest === $latestU) || (!$user->isAuthenticated()) ) 
        {
            return [];
        }
        
        # My first GDO monster query... Quite OK.
        $query = GWF_ForumPost::table()->select('board_id, thread_id, post_id');
        $query->join("JOIN gwf_forumthread ON thread_id = post_thread");
        $query->join("JOIN gwf_forumboard ON board_id = thread_board");
        $query->join("LEFT JOIN gwf_userpermission ON board_permission=perm_perm_id AND perm_user_id={$user->getID()}");
        $query->join("LEFT JOIN gwf_forumread ON read_user={$user->getID()} AND read_post=post_id");
        $query->where("post_edited > '$latestU' OR (post_edited IS NULL AND post_created > '$latestU')");
        $query->where("read_post IS NULL");
        $query->where("perm_perm_id IS NULL OR perm_user_id IS NOT NULL");
        return $query->exec()->fetchAllRows();
    }
}
