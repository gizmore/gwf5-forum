<?php $post instanceof GWF_ForumPost; $creator = $post->getCreator(); $user = GWF_User::current(); ?>
<?php
$unread = $post->isUnread($user);
$readClass = $unread ? 'gwf-forum-unread' : 'gwf-forum-read';
if ($unread) $post->markRead($user);
?>
<!-- Begin ForumPost card -->
<md-card class="<?=$readClass;?>">
  <md-card-title>
    <md-card-title-text>
      <span class="md-headline">
        <div><?= $creator->renderCell(); ?></div>
        <div class="gwf-card-date"><?= t('posted_at', [$post->displayCreated()]); ?></div>
      </span>
    </md-card-title-text>
  </md-card-title>
  <gwf-div></gwf-div>
  <md-card-content>
    <?= $post->displayMessage(); ?>
<?php if ($post->hasAttachment()) : ?>
    <div class="gwf-attachment" layout="row" flex layout-fill layout-align="left center">
      <div><?= GDO_IconButton::make()->icon('file_download')->href($post->hrefAttachment()); ?></div>
      <div><?= $post->getAttachment()->renderCell(); ?></div>
    </div>
<?php endif; ?>
      <hr/>
      <?= $post->displaySignature(); ?>
  </md-card-content>
  <gwf-div></gwf-div>
  <md-card-actions layout="row" layout-align="end center">
    <?= GDO_EditButton::make()->href($post->hrefEdit())->editable($post->canEdit($user)); ?>
    <?= GDO_Button::make('btn_reply')->icon('reply')->href($post->hrefReply()); ?>
    <?= GDO_Button::make('btn_quote')->icon('reply_all')->href($post->hrefQuote()); ?>
  </md-card-actions>
</md-card>
<!-- End ForumPost card -->
