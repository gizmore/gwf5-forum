<?php
/**
 * GWF Forum Module
 * @author gizmore
 * @since 2.0
 * @version 5.0
 */
final class Module_Forum extends GWF_Module
{
    ##############
    ### Module ###
    ##############
    public $module_priority = 55;
    public function getClasses() { return ['GWF_ForumBoard', 'GDO_ForumBoard', 'GWF_ForumThread', 'GWF_ForumPost', 'GWF_ForumRead', 'GWF_ForumThreadSubscribe', 'GWF_ForumBoardSubscribe', 'GDO_ForumSubscribe']; }
    public function onLoadLanguage() { $this->loadLanguage('lang/forum'); }
    public function onIncludeScripts()
    {
        $this->addCSS('css/gwf-forum.css');
    }
    
    ##############
    ### Config ###
    ##############
    /**
     * Let user choose a signature in settings page.
     */
    public function getUserSettings()
    {
        return array(
            GDO_Message::make('forum_signature')->utf8()->caseI()->max(512)->label('forum_signature'),
            GDO_ForumSubscribe::make('forum_subscription')->initial(GDO_ForumSubscribe::OWN),
        );
    }
    
    /**
     * Store some stats in hidden settings.
     */
    public function getUserConfig()
    {
        return array(
            GDO_Int::make('forum_posts')->unsigned()->initial('0'),
            GDO_Int::make('forum_threads')->unsigned()->initial('0'),
            GDO_DateTime::make('forum_readmark')->label('forum_readmark'),
        );
    }
    
    /**
     * Module config
     */
    public function getConfig()
    {
        return array(
            GDO_Checkbox::make('forum_guest_posts')->initial('1'),
            GDO_Checkbox::make('forum_attachments')->initial('1'),
            GDO_Level::make('forum_attachment_level')->initial('0'),
            GDO_Level::make('forum_post_level')->initial('0'),
            GDO_DateTime::make('forum_latest_post_date'),
            GDO_Int::make('forum_mail_sent_for_post')->initial('0'),
        );
    }
    public function cfgGuestPosts() { return $this->getConfigValue('forum_guest_posts'); }
    public function cfgAttachments() { return $this->getConfigValue('forum_attachments'); }
    public function cfgAttachmentLevel() { return $this->getConfigValue('forum_attachment_level'); }
    public function cfgPostLevel() { return $this->getConfigValue('forum_post_level'); }
    public function cfgLastPostDate() { return $this->getConfigVar('forum_latest_post_date'); }
    public function cfgLastPostMail() { return $this->getConfigVar('forum_mail_sent_for_post'); }
    
    ###################
    ### Permissions ###
    ###################
    public function canUpload(GWF_User $user) { return $this->cfgAttachments() && ($user->getLevel() >= $this->cfgAttachmentLevel()); }
    
    ###############
    ### Install ###
    ###############
    /**
     * Create a root board element on install.
     */
    public function onInstall()
    {
        if (!GWF_ForumBoard::getById('1'))
        {
            GWF_ForumBoard::blank(['board_title' => 'GWFv5 Forum', 'board_description' => 'Welcome to the GWFv5 Forum Module'])->insert();
        }
    }
    
    public function onWipe()
    {
        GDOCache::flush();
    }
    
    #############
    ### Hooks ###
    #############
    public function hookForumPostCreated(GWF_ForumPost $post)
    {
        $post->getThread()->getBoard()->recache();
        GWF_ForumBoard::recacheAll();
    }
    
    ##############
    ### Render ###
    ##############
    public function renderTabs() { return $this->templatePHP('tabs.php'); }
    public function onRenderFor(GWF_Navbar $navbar) { $this->templatePHP('sidebars.php', ['navbar'=>$navbar]); }
}
