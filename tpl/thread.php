<?php
$thread instanceof GWF_ForumThread;

# Posts as list
$list = GDO_List::make();
$pagemenu = GDO_PageMenu::make();
$query = GWF_ForumPost::table()->select()->where("post_thread={$thread->getID()}");
$pagemenu->filterQuery($query);
$list->query($query);
$list->listMode(GDO_List::MODE_CARD);
$list->label('list_title_thread_posts', [$thread->displayTitle(), $thread->getPostCount()]);
echo $list->render();
