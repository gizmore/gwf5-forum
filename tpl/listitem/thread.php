<?php $thread instanceof GWF_ForumThread; ?>
<?php $creator = $thread->getCreator(); ?>
<?php $user = GWF_User::current(); ?>
<?php $readClass = $thread->hasUnreadPosts($user) ? 'gwf-forum-unread' : 'gwf-forum-read'; ?>
<md-list-item class="md-3-line <?=$readClass;?>" ng-click="null" href="<?= href('Forum', 'Thread', '&thread='.$thread->getID()); ?>">
  <?= GWF_Avatar::renderAvatar($creator); ?>
  <div class="md-list-item-text" layout="column">
    <h3><?= $thread->displayTitle(); ?></h3>
    <h4><?= t('li_thread_created', [$creator->displayNameLabel()]); ?></h4>
    <p><?= $thread->displayCreated(); ?></p>
  </div>
  <?= t('thread_postcount', [$thread->getPostCount()]); ?>
  <?= GDO_Icon::iconS('arrow_right'); ?>
</md-list-item>
