<?php
/**
 * Forum thread database object.
 * @author gizmore
 */
final class GWF_ForumThread extends GDO
{
    ###########
    ### GDO ###
    ###########
    public function gdoCached() { return false; }
    public function gdoColumns()
    {
        return array(
            GDO_AutoInc::make('thread_id'),
            GDO_ForumBoard::make('thread_board')->notNull()->label('board'),
            GDO_String::make('thread_title')->utf8()->caseI()->notNull()->max(128)->label('title'),
            GDO_Int::make('thread_postcount')->unsigned()->initial('0'),
            GDO_Int::make('thread_viewcount')->unsigned()->initial('0'),
            GDO_Checkbox::make('thread_locked')->initial('0'),
            GDO_CreatedAt::make('thread_created'),
            GDO_CreatedBy::make('thread_creator'),
        );
    }
    
    ##################
    ### Permission ###
    ##################
    public function canView(GWF_User $user) { return $this->getBoard()->canView($user); }
    public function canEdit(GWF_User $user) { return $user->isStaff() || ($this->getCreatorID() === $user->getID()); }
    
    ##############
    ### Getter ###
    ##############
    /**
     * @return GWF_ForumBoard
     */
    public function getBoard() { return $this->getValue('thread_board'); }
    public function getBoardID() { return $this->getVar('thread_board'); }
    
    public function getTitle() { return $this->getVar('thread_title'); }
    
    public function getPostCount() { return $this->getVar('thread_postcount'); }
    public function getViewCount() { return $this->getVar('thread_viewcount'); }
    
    public function isLocked() { return $this->getValue('thread_locked'); }
    
    public function getCreated() { return $this->getVar('thread_created'); }
    /**
     * @return GWF_User
     */
    public function getCreator() { return $this->getValue('thread_creator'); }
    public function getCreatorID() { return $this->getVar('thread_creator'); }
    
    ##############
    ### Render ###
    ##############
    public function displayTitle() { return htmle($this->getTitle()); }
    public function displayCreated() { return tt($this->getCreated()); }
    
    public function renderList() { return GWF_Template::modulePHP('Forum', 'listitem/thread.php', ['thread'=>$this]); }

    ##############
    ### Unread ###
    ##############
    public function hasUnreadPosts(GWF_User $user)
    {
        $unread = GWF_ForumRead::getUnreadThreads($user);
        return isset($unread[$this->getID()]);
    }
    
    #############
    ### Hooks ###
    #############
    public function gdoAfterCreate()
    {
        $board = $this->getBoard();
        while ($board)
        {
            $board->increase('board_threadcount');
            $board = $board->getParent();
        }
    }
    
    #################
    ### Subscribe ###
    #################
    public function hasSubscribed(GWF_User $user)
    {
        if (GWF_UserSetting::userGet($user, 'forum_subscription') === GDO_ForumSubscribe::ALL)
        {
            return true;
        }
        return strpos($this->getForumSubscriptions($user), ",{$this->getID()},") !== false;
    }
    
    public function getForumSubscriptions(GWF_User $user)
    {
        if (!($cache = $user->tempGet('gwf_forum_thread_subsciptions')))
        {
            $cache = GWF_ForumThreadSubscribe::table()->select('GROUP_CONCAT(subscribe_thread)')->where("subscribe_user={$user->getID()}")->exec()->fetchValue();
            $cache = empty($cache) ? '' : ",$cache,";
            $user->tempSet('gwf_forum_thread_subsciptions', $cache);
            $user->recache();
        }
        return $cache;
    }
    
}
