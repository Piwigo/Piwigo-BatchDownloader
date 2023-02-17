<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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
$lang['Generate ZIP'] = '產生 ZIP 壓縮檔';
$lang['Unable to find ZipArchive PHP extension, Batch Downloader will use PclZip instead, but with degraded performance.'] = '找不到ZipArchive PHP擴展元件，將使用PclZip批次下載，但將會影響處理效率。';
$lang['User collection'] = '使用者珍藏本';
$lang['User groups'] = '用戶群組';
$lang['Warning: Only registered users can use Batch Downloader.'] = '警告：只有註冊用戶可以使用批次下載。';
$lang['Warning: ZipArchive doesn\'t accept special characters like accentuated ones, angle quotes (») and non-latin alphabets.'] = '警告：ZipArchive不接受特殊字符，如突顯的角引號(») 和非拉丁字母。';
$lang['Remove all finished downloads'] = '刪除所有已完成的下載程序';
$lang['Remove from download set'] = '從下載組別中移除';
$lang['Return to download page'] = '回到下載頁面';
$lang['Set type'] = '組別類型';
$lang['Starting download Archive #%d will destroy Archive #%d, be sure you finish the download. Continue ?'] = '開始下載壓縮包 #%d 將摧毀壓縮包 #%d，請確認您是否以完成下載。要繼續嗎？';
$lang['The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'] = '檔案下載中，如果下載程序沒有自動啟動，請<a href="%s">單擊此處</a>';
$lang['Total size'] = '總計大小';
$lang['Maximum number of photos per download set'] = '每個組別最多可下載的相片數量';
$lang['Maximum photo size'] = '相片的最大尺寸';
$lang['Maximum size of each archive (in Megabytes)'] = '每個壓縮文件的最大尺寸 (以MB為單位)';
$lang['No result'] = '沒有結果';
$lang['Number of archives'] = '壓縮檔數量';
$lang['Number of images'] = '相片數量';
$lang['Random'] = '隨機';
$lang['Download all pictures of this selection'] = '下載這個選擇所有的圖片';
$lang['Download history'] = '下載記錄';
$lang['Download info'] = '下載資訊';
$lang['Download links'] = '下載連結';
$lang['Download permissions'] = '下載權限';
$lang['Download set deleted'] = '下載組別已刪除';
$lang['Downloads'] = '下載數';
$lang['Edit the set'] = '編輯組別';
$lang['Estimated number of archives'] = '預估的檔案數量';
$lang['Estimated size'] = '預估的檔案大小';
$lang['<b>Warning:</b> all files will be deleted within %d hours'] = '<b> 警告：</b>內的所有文件都將於 %d 小時 內刪除';
$lang['Archive comment'] = '壓縮包註解';
$lang['Archive prefix'] = '壓縮包前綴';
$lang['Archives'] = '壓縮包集成';
$lang['Batch Downloader'] = '批次下載';
$lang['Cancel this download'] = '取消這個下載';
$lang['Cancel this set'] = '取消這個組別';
$lang['Confirm the download of %d pictures?'] = '確認下載這 %d 張圖片嗎？';
$lang['Delete downloads after'] = '於下載後刪除壓縮包';
$lang['Delete this set'] = '刪除這個組別';
$lang['What can be downloaded?'] = '有什麼可以下載？';
$lang['Whole gallery'] = '整本相簿';
$lang['You can not edit this set'] = '您無法編輯這個組別';
$lang['You choose to download %d pictures, but the system is limited to %d. You can edit the set, or the last %d pictures will not be downloaded.'] = '您選擇下載 %d 張圖片，但系統限制為 %d 張。您可以編輯組別，否則最後 %d 張圖片將不會被下載。';
$lang['done'] = '搞定';
$lang['download'] = '下載';
$lang['hours'] = '小時';
$lang['real number of archives can differ'] = '實際壓縮包數量可以不同';
$lang['Sorry, there is nothing to download. Some files may have been excluded because of <i title="Authorized types are : %s">filetype restrictions</i>.'] = '很抱歉，並沒有可供下載的項目。基於<i title="Authorized types are : %s">文件類型的限制</i>，有些檔案可能已被排除。';
$lang['%d MB'] = '%d MB';
$lang['Archive #%d (already downloaded)'] = '壓縮包 #%d (下載完畢)';
$lang['Archive #%d (pending)'] = '壓縮包 #%d (轉呈中)';
$lang['Archive #%d (ready)'] = '壓縮包 #%d (就緒)';
$lang['Delete previous archive when starting to download another one'] = '當開始進行一個新的下載程序，則刪除先前的壓縮包。';
$lang['Don\'t download archives through PHP'] = '不經由PHP下載壓縮包';
$lang['Force the usage of PclZip instead of ZipArchive as ZIP library'] = '強制使用 PciZip 壓縮程序以取代 ZipArchives';
$lang['It saves space on the server but doesn\'t allow to restart failed downloads.'] = '這將可節省伺服器空間，但無法使用續傳功能。';
$lang['Only check if you are experiencing corrupted archives with ZipArchive.'] = '只使用於當您遇到 ZipArchive 錯誤時。';
$lang['Only check if your host complains about high PHP usage.'] = '只使用於當您的伺服器提供者發出過量PHP使用警告時。';
$lang['Please wait, your download is being prepared. This page will automatically refresh when it is ready.'] = '請稍候，正在準備您的下載工作。完成後將會自動更新頁面。';
$lang['Preparation'] = '準備';
$lang['Unknown'] = '未知';
$lang['Any size'] = '任何尺寸';
$lang['One size'] = '一個尺寸';
$lang['Photo size'] = '照片尺寸';
$lang['Photo size choices'] = '照片尺寸的選擇';
$lang['%s plugin detected, albums will be downloadable according to permissions.'] = '檢測到 %s 插件，相冊將根據權限下載。';
$lang['%s lines printed, %s in total.'] = '印出 %s 行訊息，總共有 %s 行。';