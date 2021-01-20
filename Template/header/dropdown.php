<?php if($this->user->hasAccess('GroupOwnersListController', '*')): ?>
<li><?=$this->url->link(t('Manage my groups'), 'GroupOwnersListController', 'index', ['plugin' => 'Group_owners'])?></li>
<?php endif; ?>