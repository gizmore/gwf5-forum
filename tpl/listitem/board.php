<?php $board instanceof GWF_ForumBoard; $user = GWF_User::current(); $bid = $board->getID(); ?>
<?php
$subscribed = $board->hasSubscribed($user);
$subscribeClass = $subscribed ? 'gwf-forum gwf-forum-subscribed' : 'gwf-forum';
$readClass = $board->hasUnreadPosts($user) ? 'gwf-forum-unread' : 'gwf-forum-read';
?>
<md-list-item class="md-3-line <?=$readClass;?> <?=$subscribeClass;?>" ng-click="null" href="<?= href('Forum', 'Boards', '&board='.$bid); ?>">
  <div class="md-list-item-text" layout="column">
    <h3><?= $board->displayName(); ?></h3>
    <h4><?= $board->displayDescription(); ?></h4>
    <p><?= t('board_stats', [$board->getThreadCount(), $board->getPostCount()]); ?></p>
  </div>

  <?= GDO_Icon::iconS('arrow_right'); ?>
  <?php $href = $subscribed ? href('Forum', 'Unsubscribe', '&board='.$bid) : href('Forum', 'Subscribe', '&board='.$bid)?>
  <?= GDO_IconButton::make()->href($href)->icon('email'); ?>
      
</md-list-item>
