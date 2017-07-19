<?php
/**
 * A board inherits from GWF_Tree.
 * @author gizmore
 * @see GWF_Tree
 * @see GWF_ForumThread
 * @see GWF_ForumPost
 */
final class GWF_ForumBoard extends GWF_Tree
{
    ############
    ### Tree ###
    ############
    public function gdoTreePrefix() { return 'board'; }

    ###########
    ### GDO ###
    ###########
    public function gdoCached() { return true; }  # GDO Cache is a good idea for Thread->getBoard()
    public function memCached() { return true; } # uses cacheall in memcached (see further down), so no single row storage for memcached
    public function gdoColumns()
    {
        return array_merge(array(
            GDO_AutoInc::make('board_id'),
            GDO_String::make('board_title')->notNull()->utf8()->caseI()->label('title')->max(64),
            GDO_String::make('board_description')->notNull()->utf8()->caseI()->label('description')->max(256),
            GDO_Permission::make('board_permission'),
            GDO_CreatedAt::make('board_created'),
            GDO_CreatedBy::make('board_creator'),
            GDO_Checkbox::make('board_allow_threads')->initial('0'),
            GDO_Int::make('board_threadcount')->initial('0'),
            GDO_Int::make('board_postcount')->initial('0'),
        ), parent::gdoColumns());
    }

    ##############
    ### Getter ###
    ##############
    public function allowsThreads() { return $this->getValue('board_allow_threads'); }
    public function getTitle() { return $this->getVar('board_title'); }
    public function getDescription() { return $this->getVar('board_description'); }
    public function getThreadCount() { return $this->getVar('board_threadcount'); }
    public function getPostCount() { return $this->getVar('board_postcount'); }
    
    public function getPermission() { return $this->getValue('board_permission'); }
    public function getPermissionID() { return $this->getVar('board_permission'); }
    
    ##################
    ### Permission ###
    ##################
    public function needsPermission() { return $this->getPermissionID() !== null; }
    public function canView(GWF_User $user) { return $this->needsPermission() ? $user->hasPermissionID($this->getPermissionID()) : true; }
    
    ##############
    ### Render ###
    ##############
    public function displayName() { return htmle($this->getTitle()); }
    public function displayDescription() { return htmle($this->getDescription()); }
    public function renderList() { return GWF_Template::modulePHP('Forum', 'listitem/board.php', ['board'=>$this]); }
    
    #############
    ### Cache ###
    #############
    public function all()
    {
        if (false === ($cache = GDOCache::get('gwf_forumboard_all')))
        {
            $cache = $this->queryAll();
            GDOCache::set('gwf_forumboard_all', $cache);
        }
        return $cache;
    }
    
    public function recacheAll()
    {
        GDOCache::unset('gwf_forumboard_all');
    }
    
    public function queryAll()
    {
        return self::table()->select()->order('board_left')->exec()->fetchAllArray2dObject();
    }

    public function gdoAfterCreate()
    {
        $this->recacheAll();
        parent::gdoAfterCreate();
    }
    
    public function increaseCounters(int $threadsBy, int $postsBy)
    {
        GWF_Log::logDebug(sprintf('GWF_ForumBoard::increaseCounters(%s, %s) ID:%s', $threadsBy, $postsBy, $this->getID()));
        $this->increase('board_threadcount', $threadsBy);
        $this->increase('board_postcount', $postsBy);
        if ($parent = $this->getParent())
        {
            $parent->increaseCounters($threadsBy, $postsBy);
        }
    }
    
    public function hasUnreadPosts(GWF_User $user)
    {
        $unread = GWF_ForumRead::getUnreadBoards($user);
        return self::hasBoardUnreadPosts($this, $unread);
    }

    public static function hasBoardUnreadPosts(GWF_ForumBoard $board, array $unread)
    {
        if (isset($unread[$board->getID()]))
        {
            return true;
        }
        
        foreach ($board->children as $child)
        {
            if (self::hasBoardUnreadPosts($child, $unread))
            {
                return true;
            }
        }
        
        return false;
    }
}
