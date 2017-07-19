<?php
$bar = GDO_Toolbar::make();
$user = GWF_User::current();
$boards = GWF_ForumBoard::table()->full()[0];
$board = $boards[Common::getRequestString('board', '1')];

# Header Create Board Button
if ($user->isStaff())
{
    $bar->addField(GDO_IconButton::make()->icon('add')->href(href('Forum', 'CRUDBoard', '&board='.$board->getID())));
}

# Header Middle Board Selection
$links = [];
$p = $board;
while ($p)
{
    $link = GDO_Link::make()->rawlabel($p->displayName())->href(href('Forum', 'Boards', '&board='.$p->getID()));
    array_unshift($links, $link);
    $p = $p->getParent();
}
$bar->addFields($links);

# Header Edit button. Either edit board or thread
if ($user->isStaff())
{
    if (Common::getGetString('me')==='Boards')
    {
        $bar->addField(GDO_IconButton::make()->icon('edit')->href(href('Forum', 'CRUDBoard', '&id='.$board->getID())));
    }
    else
    {
        $bar->addField(GDO_IconButton::make()->icon('edit')->href(href('Forum', 'EditThread', '&id='.Common::getGetString('thread'))));
    }
}


# Render Bar
echo $bar->renderCell();
