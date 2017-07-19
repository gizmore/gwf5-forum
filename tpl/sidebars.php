<?php $navbar instanceof GWF_Navbar; ?>
<?php
if ($navbar->isLeft())
{
    $user = GWF_User::current();
    $module = Module_Forum::instance();
    if ($root = GWF_ForumBoard::getById('1'))
    {
        $posts = $root->getPostCount();
        $link = GDO_Link::make()->label('link_forum', [$posts])->href(href('Forum', 'Boards'));
        if ($user->isAuthenticated())
        {
            if (GWF_ForumRead::countUnread($user) > 0)
            {
                $link->icon('notifications_active');
            }
        }
        $navbar->addField($link);
    }
}
if ($navbar->isTop())
{
    if (mo()==='Forum')
    {
        $navbar->addField(GDO_IconButton::make()->icon('settings')->href(href('Account', 'Settings', '&module=Forum')));
    }
}
