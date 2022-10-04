<dt>{$block->get_title()}</dt>
<dd>
	<ul>
		{foreach from=$block->data item=link}
		<li class="nav-item">
      <a href="{$link.URL}" title="{$link.TITLE}" rel="nofollow">{$link.NAME}</a>
      <span class="menuInfoCat">[{$link.COUNT}]</span>
    </li>
		{/foreach}
	</ul>
</dd>