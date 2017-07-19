<?php
final class Forum_CRUDBoard extends GWF_MethodCrud
{
    public function gdoTable() { return GWF_ForumBoard::table(); }
    public function hrefList() { return href('Forum', 'Boards', '&board='.Common::getRequestInt('board')); }
   
    public function canCreate(GDO $gdo) { return GWF_User::current()->isStaff(); }
    public function canUpdate(GDO $gdo) { return GWF_User::current()->isStaff(); }
    public function canDelete(GDO $gdo) { return GWF_User::current()->isAdmin(); }
    
    public function execute()
    {
        $response = parent::execute();
        $tabs = Module_Forum::instance()->renderTabs();
        return $tabs->add($response);
    }
    
    public function createForm(GWF_Form $form)
    {
        $gdo = GWF_ForumBoard::table();
        $boardId = Common::getRequestString('board');
        $form->addFields(array(
            $gdo->gdoColumn('board_title'),
            $gdo->gdoColumn('board_description'),
            GDO_ForumBoard::make('board_parent')->label('parent')->notNull()->initial($boardId),
            $gdo->gdoColumn('board_permission'),
            $gdo->gdoColumn('board_allow_threads'),
        ));
        $this->createFormButtons($form);
    }
    
    public function afterUpdate(GWF_Form $form)
    {
        GWF_ForumBoard::recacheAll();
        $this->gdo->recache();
    }
    
}
