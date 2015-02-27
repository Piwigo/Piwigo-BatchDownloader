<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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
$lang['Random'] = '随机';
$lang['Number of images'] = '图片数量';
$lang['Number of archives'] = '压缩文件数量';
$lang['Remove from download set'] = '从下载集合中删除';
$lang['Warning: ZipArchive doesn\'t accept special characters like accentuated ones, angle quotes (») and non-latin alphabets.'] = '警告：ZipArchive 不接受特殊字符，例如：重音符号、角引号(»)和非拉丁字母。';
$lang['real number of archives can differ'] = '实际的压缩文件数量可能不同';
$lang['hours'] = '小时';
$lang['download'] = '下载';
$lang['done'] = '完成';
$lang['You choose to download %d pictures, but the system is limited to %d. You can edit the set, or the last %d pictures will not be downloaded.'] = '你选择要下载 %d 张图片，但是系统的限制是 %d 张。你需要重新编辑该集合，否则最后的 %d 张图片将不会被下载。';
$lang['You can not edit this set'] = '你不能编辑这个集合';
$lang['Whole gallery'] = '整个相册';
$lang['User collection'] = '用户精选集';
$lang['User groups'] = '用户组';
$lang['Total size'] = '总大小';
$lang['The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'] = '压缩文件下载中，如果下载没有自动开始，请<a href="%s">点击这里</a>';
$lang['Set type'] = '设置类型';
$lang['Return to download page'] = '返回下载页面';
$lang['Download set deleted'] = '下载集合已删除';
$lang['Remove all finished downloads'] = '删除所有已完成下载';
$lang['Estimated number of archives'] = '预估的压缩包数量';
$lang['Downloads'] = '下载';
$lang['Archives'] = '压缩文件';
$lang['Archive prefix'] = '压缩文件前缀';
$lang['Archive comment'] = '压缩文件注释';
$lang['Maximum size of each archive (in Megabytes)'] = '单个压缩文件的最大大小（Mb）';
$lang['Maximum photo size'] = '最大图片大小';
$lang['Maximum number of photos per download set'] = '每个下载集合的最大图片数量';
$lang['Generate ZIP'] = '生成ZIP';
$lang['Estimated size'] = '预估的大小';
$lang['Edit the set'] = '编辑本集合';
$lang['Download permissions'] = '下载权限';
$lang['Download links'] = '下载链接';
$lang['Download info'] = '下载信息';
$lang['Download history'] = '下载历史';
$lang['Download all pictures of this selection'] = '下载所有选定图片';
$lang['Delete this set'] = '删除本集合';
$lang['Delete downloads after'] = '在此之后删除压缩文件：';
$lang['Confirm the download of %d pictures?'] = '确定下载 %d 张图片?';
$lang['Cancel this set'] = '取消本集合';
$lang['Cancel this download'] = '取消本次下载';
$lang['Batch Downloader'] = 'Batch Downloader';
$lang['<b>Warning:</b> all files will be deleted within %d hours'] = '<b>警告：</b> 所有文件将会在 %d 小时内被删除';
$lang['Unable to find ZipArchive PHP extension, Batch Downloader will use PclZip instead, but with degraded performance.'] = '未发现ZipArchive的PHP扩展，Batch Downloader将使用PclZip作为替代，但表现将不如前者。';
$lang['Starting download Archive #%d will destroy Archive #%d, be sure you finish the download. Continue ?'] = '开始下载压缩文件 #%d 将破坏压缩文件 #%d，请确认你已完成此前的下载。继续？';
$lang['Warning: Only registered users can use Batch Downloader.'] = '警告：只有注册用户才能使用Batch Downloader。';
$lang['What can be downloaded?'] = '可以下载什么？';
$lang['No result'] = '没有结果';
$lang['%d MB'] = '%d MB';
$lang['Archive #%d (already downloaded)'] = '压缩文件 #%d(已经下载)';
$lang['Archive #%d (pending)'] = '压缩文件 #%d(等待中)';
$lang['Archive #%d (ready)'] = '压缩文件 #%d(准备中)';
$lang['Please wait, your download is being prepared. This page will automatically refresh when it is ready.'] = '请稍等，正在准备下载。准备完成后本页面将自动重载。';
$lang['Preparation'] = '准备';
$lang['Sorry, there is nothing to download. Some files may have been excluded because of <i title="Authorized types are : %s">filetype restrictions</i>.'] = '抱歉，没有可下载的文件。某些文件可能因为 <i title="被认可的类型 : %s">文件类型限制</i>而被排除了。';
$lang['Unknown'] = '未知';
$lang['Only check if you are experiencing corrupted archives with ZipArchive.'] = '仅在使用ZipArchive时遇到压缩文件损坏的情况下勾选。';
$lang['Only check if your host complains about high PHP usage.'] = '仅在服务器报告高PHP使用量时勾选。';
$lang['Delete previous archive when starting to download another one'] = '在开始下载另一个压缩文件时删除之前的压缩文件';
$lang['Don\'t download archives through PHP'] = '不要通过PHP下载压缩文件';
$lang['Force the usage of PclZip instead of ZipArchive as ZIP library'] = 'Force the usage of PclZip instead of ZipArchive as ZIP library';
$lang['It saves space on the server but doesn\'t allow to restart failed downloads.'] = '这将节省服务器空间，但将不能重启已失败的下载。';
$lang['Any size'] = '任何尺寸';
$lang['One size'] = '一个尺寸';
$lang['Photo size'] = '相片尺寸';
$lang['Photo size choices'] = '选择的尺寸';
$lang['%s plugin detected, albums will be downloadable according to permissions.'] = '检测到 %s 插件，将下载有相应权限的相册。';