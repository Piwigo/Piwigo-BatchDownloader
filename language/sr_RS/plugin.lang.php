<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2014 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+
$lang['User collection'] = 'Корисникова збирка';
$lang['done'] = 'готово';
$lang['download'] = 'преузимање';
$lang['hours'] = 'сати';
$lang['real number of archives can differ'] = 'стваран број архива може бити различит';
$lang['Warning: ZipArchive doesn\'t accept special characters like accentuated ones, angle quotes (») and non-latin alphabets.'] = 'Упозорење: ZipArchive не прихвата специјалне знакове, као што су акценти, угласти наводници (») и слова изван латинице.';
$lang['What can be downloaded?'] = 'Шта могу да преузмем?';
$lang['Whole gallery'] = 'Цела галерија';
$lang['You can not edit this set'] = 'Не можете уредити овај сет';
$lang['You choose to download %d pictures, but the system is limited to %d. You can edit the set, or the last %d pictures will not be downloaded.'] = 'Изабрали сте преузимање %d слика, а систем допушта само %d. Уколико не измените изабрани сет, последњих %d слика неће бити преузето.';
$lang['Sorry, there is nothing to download. Some files may have been excluded because of <i title="Authorized types are : %s">filetype restrictions</i>.'] = 'Немате ништа за преузимање. Можда су неке датотеке искључене због <i title="Ауторизовати типови: %s">ограничења на врсте датотеке</i>';
$lang['Starting download Archive #%d will destroy Archive #%d, be sure you finish the download. Continue ?'] = 'Преузимање архиве #%d ће уклонити постојећу архиву #%d. Најпре проверите да ли је претходна архива преузета. Да наставим?';
$lang['The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'] = 'Сада преузимате архиву. Уколико преузимање није отпочело, кликните на <a href="%s">овај линк</a>';
$lang['Total size'] = 'Укупна величина';
$lang['Unable to find ZipArchive PHP extension, Batch Downloader will use PclZip instead, but with degraded performance.'] = 'Не могу да пронађем ZipArchive PHP проширење. Групно преузимање ће користити нешто спорији PclZip.';
$lang['Unknown'] = 'Непознато';
$lang['User groups'] = 'Групе корисника';
$lang['Warning: Only registered users can use Batch Downloader.'] = 'Упозорење: Само регистровани корисници могу користити Групно преузимање';
$lang['Set type'] = 'Врста сета';
$lang['Only check if you are experiencing corrupted archives with ZipArchive.'] = 'Изаберите ову опцију уколико очекујете оштећене ZipArchive архиве.';
$lang['Only check if your host complains about high PHP usage.'] = 'Изаберите ову опцију уколико се власник сервера жали на прекомерну употребу PHP-а.';
$lang['Photo size'] = 'Величина фотографије';
$lang['Photo size choices'] = 'Избори величине фотографије';
$lang['Please wait, your download is being prepared. This page will automatically refresh when it is ready.'] = 'Сачекајте док преузимање не буде спремно. Ова страница ће се сама освежити у том тренутку.';
$lang['Preparation'] = 'Припрема';
$lang['Random'] = 'Насумично';
$lang['Remove all finished downloads'] = 'Уклони сва успешна преузимања';
$lang['Remove from download set'] = 'Уклони из сета за преузимање';
$lang['Return to download page'] = 'Назад на страницу за преузимање';
$lang['Force the usage of PclZip instead of ZipArchive as ZIP library'] = 'Приморај PclZip уместо ZipArchive при прављењу архива';
$lang['Generate ZIP'] = 'Направи ZIP';
$lang['It saves space on the server but doesn\'t allow to restart failed downloads.'] = 'Овим ћете сачувати простор на серверу, али нећете моћи да наставите преузимање неуспешно преузетих архива.';
$lang['Maximum number of photos per download set'] = 'Највећи број слика по сету за преузимање';
$lang['Maximum photo size'] = 'Највећа величина слика';
$lang['Maximum size of each archive (in Megabytes)'] = 'Највећа величина сваке архиве (у мегабајтима)';
$lang['No result'] = 'Нема резултата';
$lang['Number of archives'] = 'Број архива';
$lang['Number of images'] = 'Број фотографија';
$lang['One size'] = 'Једна величина';
$lang['Download links'] = 'Линкови за преузимање';
$lang['Download permissions'] = 'Дозволе за преузимање';
$lang['Download set deleted'] = 'Сет за преузимање је обрисан';
$lang['Downloads'] = 'Преузимања';
$lang['Edit the set'] = 'Уреди сет';
$lang['Estimated number of archives'] = 'Процењени број архива';
$lang['Estimated size'] = 'Процењена величина';
$lang['%d MB'] = '%d MB';
$lang['<b>Warning:</b> all files will be deleted within %d hours'] = '<b>Упозорење:</b> све датотеке ће бити обрисане за %d сата';
$lang['Any size'] = 'Било која величина';
$lang['Archive #%d (already downloaded)'] = 'Архивирај #%d (већ преузето)';
$lang['Archive #%d (pending)'] = 'Архивирај #%d (на чекању)';
$lang['Archive #%d (ready)'] = 'Архивирај #%d (спремно)';
$lang['Archive comment'] = 'Коментар за архиву';
$lang['Archive prefix'] = 'Префикс архиве';
$lang['Archives'] = 'Архиве';
$lang['Batch Downloader'] = 'Групно преузимање';
$lang['Cancel this download'] = 'Откажи ово преузимање';
$lang['Cancel this set'] = 'Откажи овај сет';
$lang['Confirm the download of %d pictures?'] = 'Да ли желите да преузмете %d слика?';
$lang['Delete downloads after'] = 'Избриши архиве након преузимања';
$lang['Delete previous archive when starting to download another one'] = 'Избриши претходне архиве по почетку преузимања наредне';
$lang['Delete this set'] = 'Обриши овај сет';
$lang['Don\'t download archives through PHP'] = 'Не преузимај архиве преко PHP-а';
$lang['Download all pictures of this selection'] = 'Преузми све изабране слике';
$lang['Download history'] = 'Историјат преузимања';
$lang['Download info'] = 'Подаци о преузимању';
$lang['%s plugin detected, albums will be downloadable according to permissions.'] = 'Пошто је пронађен додатак %s, албуми ћете моћи да преузмете у складу са издатим овлађћењима.';
