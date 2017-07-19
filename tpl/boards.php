<?php
$table = GWF_ForumBoard::table();
$boards = $table->full()[0]; # Get tree structure
$board = $boards[Common::getRequestString('board', '1')];
$board instanceof GWF_ForumBoard;

# Children boards as list.
$list = GDO_List::make();
$list->result(new GDOArrayResult($board->children, $table));
$list->listMode(GDO_List::MODE_LIST);
$list->rawlabel($board->displayDescription());
echo $list->render();

# Create thread button
if ($board->allowsThreads())
{
    echo GDO_Button::make('btn_create_thread')->icon('create')->href(href('Forum', 'CreateThread', '&board='.$board->getID()));
}

# Threads as list
$list = GDO_List::make();
$pagemenu = GDO_PageMenu::make();
$query = GWF_ForumThread::table()->select()->where("thread_board={$board->getID()}");
$pagemenu->filterQuery($query);
$list->query($query);
$list->listMode(GDO_List::MODE_LIST);
// $list->label('list_title_board_threads', [$board->getThreadCount()]);
echo $list->render();
