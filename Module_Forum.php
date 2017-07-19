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
    public function getClasses() { return ['GWF_ForumBoard', 'GDO_ForumBoard', 'GWF_ForumThread', 'GWF_ForumPost']; }
    public function onLoadLanguage() { $this->loadLanguage('lang/forum'); }
    
    ##############
    ### Config ###
    ##############
    /**
     * Let user choose a signature in settings page.
     */
    public function getUserConfig()
    {
        return array(
            GDO_Message::make('forum_signature')->utf8()->caseI()->max(512),
        );
    }
    
    /**
     * Store some stats in hidden settings.
     */
    public function getUserSettings()
    {
        return array(
            GDO_Int::make('forum_posts')->unsigned()->initial('0'),
            GDO_Int::make('forum_threads')->initial('0'),
            GDO_DateTime::make('forum_threads')->initial('0'),
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
        );
    }
    public function cfgGuestPosts() { return $this->getConfigValue('forum_guest_posts'); }
    public function cfgAttachments() { return $this->getConfigValue('forum_attachments'); }
    public function cfgAttachmentLevel() { return $this->getConfigValue('forum_attachment_level'); }
    public function cfgPostLevel() { return $this->getConfigValue('forum_post_level'); }
    
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
    
    ##############
    ### Render ###
    ##############
    public function renderTabs() { return $this->templatePHP('tabs.php'); }
    public function onRenderFor(GWF_Navbar $navbar) { $this->templatePHP('sidebars.php', ['navbar'=>$navbar]); }
}
