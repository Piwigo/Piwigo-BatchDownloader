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
$lang['Cancel this download'] = 'Letöltés megszakítása';
$lang['Download all pictures of this selection'] = 'Az összes kiválasztott kép letöltése';
$lang['User collection'] = 'Felhasználó készlet';
$lang['Confirm the download of %d pictures?'] = '%d kép letöltése. Megerősíti?';
$lang['The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'] = 'Archívum letöltése. Ha a letöltés nem indul el automatikusan, <a href="%s">kattintson ide</a>';
$lang['Remove all finished downloads'] = 'Összes letöltés befejeztével eltávolítás';
$lang['Remove from download set'] = 'Letöltés készlet eltávolítása';
$lang['Archive comment'] = 'Archívum megjegyzés';
$lang['Archive prefix'] = 'Archívum előtag';
$lang['Archives'] = 'Archívumok';
$lang['Delete downloads after'] = 'Törlés letöltés után';
$lang['Maximum number of photos per download set'] = 'Képek maximális száma / letöltés készlet';
$lang['Maximum photo size'] = 'Maximális képméret';
$lang['Maximum size of each archive (in Megabytes)'] = 'Archívum maximális mérete (Megabájtban)';
$lang['Number of archives'] = 'Archívumok száma';
$lang['Number of images'] = 'Képek száma';
$lang['User groups'] = 'Felhasználói csoportok';
$lang['Whole gallery'] = 'Teljes galéria';
$lang['done'] = 'kész';
$lang['download'] = 'letöltés';
$lang['hours'] = 'óra';
$lang['Total size'] = 'Teljes méret';
$lang['Set type'] = 'Típus kiválasztása';
$lang['Generate ZIP'] = 'ZIP fájl létrehozása';
$lang['Random'] = 'Véletlen';
$lang['Return to download page'] = 'Vissza a letöltési oldalra';
$lang['Batch Downloader'] = 'Kötegelt letöltő';
$lang['Delete this set'] = 'Készlet törlése';
$lang['Download history'] = 'Letöltés előzmények';
$lang['Download info'] = 'Letöltés adatok';
$lang['Download links'] = 'Letöltés hivatkozások';
$lang['Download permissions'] = 'Letöltés engedélyek';
$lang['Download set deleted'] = 'Letöltés készlet törölve';
$lang['Downloads'] = 'Letöltések';
$lang['Edit the set'] = 'Készlet szerkesztése';
$lang['Estimated size'] = 'Becsült méret';
$lang['You can not edit this set'] = 'Nem tudja szerkeszteni a készletet';
$lang['<b>Warning:</b> all files will be deleted within %d hours'] = '<b>Figyelem:</b> az összes fájl törlésre kerül %d órán belül';
$lang['Estimated number of archives'] = 'Archívum becsült száma';
$lang['You choose to download %d pictures, but the system is limited to %d. You can edit the set, or the last %d pictures will not be downloaded.'] = '%d képet választott ki letöltésre, de a rendszer csak %d képet tud letölteni. Szerkessze a készletet, vagy az utolsó %d kép nem fog letöltődni.';
$lang['Unable to find ZipArchive PHP extension, Batch Downloader will use PclZip instead, but with degraded performance.'] = 'Nem található ZipArchive PHP kiterjesztés, a Batch Downloader a pclzip-et fogja használni helyette, de csökkenti a teljesítményt.';
$lang['Warning: Only registered users can use Batch Downloader.'] = 'Figyelem: Csak regisztrált felhasználók használhatják a Batch Downloader-t.';
$lang['Warning: ZipArchive doesn\'t accept special characters like accentuated ones, angle quotes (») and non-latin alphabets.'] = 'Figyelem: a ZIP archívum nem tartalmazhat speciális, ékezetes karaktereket, idézőjelet, (») vagy a nem latin abc betűit.';
$lang['Cancel this set'] = 'Készlet visszavonása';
$lang['Starting download Archive #%d will destroy Archive #%d, be sure you finish the download. Continue ?'] = 'Archívum #%d letöltése az Archívum megsemmisítéséhez vezet, győződj meg róla, hogy befejezted a letöltést mielőtt folytatod';
$lang['What can be downloaded?'] = 'Mi letölthető?';
$lang['real number of archives can differ'] = 'Az archívum valós számai különbözőek lehetnek';
$lang['No result'] = 'Nincs találat';
$lang['%d MB'] = '%d MB';
$lang['Archive #%d (already downloaded)'] = ' #%d archívum (letölthető)';
$lang['Archive #%d (pending)'] = '#%d archívum (folyamatban)';
$lang['Archive #%d (ready)'] = '#%d archívum (kész)';
$lang['Please wait, your download is being prepared. This page will automatically refresh when it is ready.'] = 'Kérem várjon, a letöltés készül. Amikor kész, az oldal automatikusan frissül.';
$lang['Preparation'] = 'Előkészítés';
$lang['Unknown'] = 'Ismeretlen';
$lang['Delete previous archive when starting to download another one'] = 'Újabb archívum letöltés indításakor törölje az előzőt';
$lang['Don\'t download archives through PHP'] = 'Ne töltsön le archívumokat PHP-n keresztül';
$lang['It saves space on the server but doesn\'t allow to restart failed downloads.'] = 'Helyet takarít meg a szerveren, de nem teszi lehetővé a sikertelen letöltések újraindítását.';
$lang['Only check if you are experiencing corrupted archives with ZipArchive.'] = 'Csak akkor használja, ha azt tapasztalja, hogy sérült a ZipArchive archívum.';
$lang['Only check if your host complains about high PHP usage.'] = 'Csak akkor válassza, ha a szolgáltató reklamál a magas PHP hatnálat miatt.';
$lang['Sorry, there is nothing to download. Some files may have been excluded because of <i title="Authorized types are : %s">filetype restrictions</i>.'] = 'Sajnálom, nincs mit letölteni. Egyes fájlok kizárásra kerültek. <i title="Authorized types are : %s">nem támogatott fájltípus</i>.';
$lang['Force the usage of PclZip instead of ZipArchive as ZIP library'] = 'PclZip használatának kényszerítése a ZipArchive helyett, mint ZIP könyvtár';
$lang['Any size'] = 'Minden méret';
$lang['One size'] = 'Egy méret';
$lang['Photo size'] = 'Kép méret';
$lang['Photo size choices'] = 'Kép méret választás';
$lang['%s plugin detected, albums will be downloadable according to permissions.'] = '%s bővítmény aktiválva, az albumok az engedélyek szerint tölthetők le. ';