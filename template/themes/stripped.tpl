<div class="titrePage">
	<div class="browsePath">
		<span id="menuswitcher" title="{'Show/hide menu'|@translate}">{'Menu'|@translate}</span><span class="arrow"> »</span>
		<h2>{$TITLE}</h2>
	</div>
</div>

<div id="content" {if !$stripped.hideMenu}class="menuShown"{/if}>
{$MENUBAR}
<div id="content_cell">