<?php
final class Forum_Boards extends GWF_Method
{
    public function execute()
    {
        $tabs = Module_Forum::instance()->renderTabs();
        return $tabs->add($this->templatePHP('boards.php'));
    }
}
