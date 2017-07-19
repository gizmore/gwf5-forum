<?php $board instanceof GWF_ForumBoard; $user = GWF_User::current(); ?>
<?php
$readClass = $board->hasUnreadPosts($user) ? 'gwf-forum-unread' : 'gwf-forum-read';
?>
<md-list-item class="md-3-line <?=$readClass;?>" ng-click="null" href="<?= href('Forum', 'Boards', '&board='.$board->getID()); ?>">
  <div class="md-list-item-text" layout="column">
    <h3><?= $board->displayName(); ?></h3>
    <h4><?= $board->displayDescription(); ?></h4>
    <p><?= t('board_stats', [$board->getThreadCount(), $board->getPostCount()]); ?></p>
  </div>
  
  <?= GDO_Icon::iconS('arrow_right'); ?>
      
</md-list-item>
