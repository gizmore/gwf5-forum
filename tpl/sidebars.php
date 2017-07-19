<?php $navbar instanceof GWF_Navbar; ?>
<?php
if ($navbar->isLeft())
{
    $navbar->addField(GDO_Link::make('link_forum')->href(href('Forum', 'Boards')));
}
