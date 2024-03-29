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
$lang['What can be downloaded?'] = 'Vad kan laddas ned?';
$lang['Whole gallery'] = 'Hela galleri';
$lang['You can not edit this set'] = 'Du kan inte redigera det här paketet';
$lang['You choose to download %d pictures, but the system is limited to %d. You can edit the set, or the last %d pictures will not be downloaded.'] = 'Du valde att ladda ned %d bilder, men systemet är begränsat till %d. Om du inte redigerar paketet så kommer de sist tillagda filerna att uteslutas från nedladdningen.';
$lang['done'] = 'klar';
$lang['download'] = 'ladda ned';
$lang['hours'] = 'timmar';
$lang['real number of archives can differ'] = 'verkligt antal arkiv kan variera';
$lang['Generate ZIP'] = 'Skapa ZIP-fil';
$lang['Maximum number of photos per download set'] = 'Max antal bilder/nedladdningspaket';
$lang['Maximum photo size'] = 'Max bildstorlek';
$lang['Maximum size of each archive (in Megabytes)'] = 'Max storlek/arkiv (i MB)';
$lang['No result'] = 'Inga resultat';
$lang['Number of archives'] = 'Antal arkiv';
$lang['Number of images'] = 'Antal bilder';
$lang['Random'] = 'Slumpmässig';
$lang['Remove all finished downloads'] = 'Ta bort alla färdiga nedladdningar';
$lang['Remove from download set'] = 'Ta bort från nedladdningspaket';
$lang['Return to download page'] = 'Återgå till nedladdningssida';
$lang['Set type'] = 'Paket-typ';
$lang['Starting download Archive #%d will destroy Archive #%d, be sure you finish the download. Continue ?'] = 'Start av arkivnedladdning #%d förstör arkiv #%d, är du säker på att du vill avsluta nedladdning.
Fortsätt?';
$lang['The archive is downloading, if the download doesn\'t start automatically please <a href="%s">click here</a>'] = 'Arkivet laddas ned, om nedladdningen inte startar automatiskt vänligen <a href="%s">Klicka här</a>';
$lang['Total size'] = 'Total storlek';
$lang['Unable to find ZipArchive PHP extension, Batch Downloader will use PclZip instead, but with degraded performance.'] = 'ZipArchive PHP extension kan inte hittas, Batchnedladdaren kommer att använda PclZip istället, men då med lägre prestanda.';
$lang['User collection'] = 'Användarsamling';
$lang['User groups'] = 'Använargrupper';
$lang['Warning: Only registered users can use Batch Downloader.'] = 'Varning: Bara registrerade användare kan bruka Batchnedladdaren.';
$lang['Warning: ZipArchive doesn\'t accept special characters like accentuated ones, angle quotes (») and non-latin alphabets.'] = 'Varning: Zip arkiv accepterar inte specialtecken i filnam.';
$lang['<b>Warning:</b> all files will be deleted within %d hours'] = '<b>Varning:</b> alla filer raderas innom %d timmar';
$lang['Archive comment'] = 'Arkivkomentar';
$lang['Archive prefix'] = 'Arkivprefix';
$lang['Archives'] = 'Arkiv';
$lang['Batch Downloader'] = 'Batchnedladdare';
$lang['Cancel this download'] = 'Avbryt nedladdning';
$lang['Cancel this set'] = 'Avbryt det här paketet';
$lang['Confirm the download of %d pictures?'] = 'Bekräfta dedladdning av %d bilder?';
$lang['Delete downloads after'] = 'Radera nedladdningar efter';
$lang['Delete this set'] = 'Radera det här paketet';
$lang['Download all pictures of this selection'] = 'Ladda ned alla valda bilder';
$lang['Download history'] = 'Nedladdningshistorik';
$lang['Download info'] = 'Nedladdningsinfo';
$lang['Download links'] = 'Nedladdningslänkar';
$lang['Download permissions'] = 'Nedladdningsbefogenhet';
$lang['Download set deleted'] = 'Nedladdningspaket raderat';
$lang['Downloads'] = 'Nedladdningar';
$lang['Edit the set'] = 'Redigera paket';
$lang['Estimated number of archives'] = 'Beräknat antal arkiv';
$lang['Estimated size'] = 'Beräknad storlek';
$lang['%d MB'] = '%d MB';
$lang['Archive #%d (already downloaded)'] = 'Arkiv #%d (redan nedladdat)';
$lang['Archive #%d (pending)'] = 'Arkiv #%d (kvar)';
$lang['Archive #%d (ready)'] = 'Arkiv #%d (klart)';
$lang['Delete previous archive when starting to download another one'] = 'Ta bort det förra arkivet när nedladdningen av ett annat arkiv startas';
$lang['Don\'t download archives through PHP'] = 'Ladda inte ned arkiv via PHP';
$lang['Force the usage of PclZip instead of ZipArchive as ZIP library'] = 'Tvinga användande av PclZip istället för ZipArchive som ZIP-bibliotek';
$lang['It saves space on the server but doesn\'t allow to restart failed downloads.'] = 'Det sparar plats på servern men tillåter inte omstart av misslyckade nedladdningar';
$lang['Only check if you are experiencing corrupted archives with ZipArchive.'] = 'Markera endast om du upplever felaktiga arkiv med ZipArchive';
$lang['Only check if your host complains about high PHP usage.'] = 'Markera endast om din värddator klagar på högt PHP-användande';
$lang['Please wait, your download is being prepared. This page will automatically refresh when it is ready.'] = 'Vänligen vänta, din nedladdning förbereds. Denna sida kommer att uppdateras automatiskt när den är klar.';
$lang['Preparation'] = 'Förberedelse';
$lang['Sorry, there is nothing to download. Some files may have been excluded because of <i title="Authorized types are : %s">filetype restrictions</i>.'] = 'Ursäkta, det finns inget att ladda ned. Några filer kan ha undantagits på grund av <i title="Tillåtna typer är : %s">filtypsbegränsningar</i>';
$lang['Unknown'] = 'Okänd';
$lang['Any size'] = 'Vilken storlek som helst';
$lang['One size'] = 'En storlek';
$lang['Photo size'] = 'Foto storlek';
$lang['Photo size choices'] = 'Foto storleks val';
$lang['%s plugin detected, albums will be downloadable according to permissions.'] = '%s plugin upptäckt, albumen blir nedladdade baserat på rättigheter';
$lang['%s lines printed, %s in total.'] = '%s linjer utskrivna, %s totalt.';
$lang['pending'] = 'väntande';
$lang['rejected'] = 'avvisad';
$lang['Set'] = 'Ställ in';
$lang['Set size'] = 'Ställ in storlek';
$lang['Size of photos'] = 'Storlek på foton';
$lang['Status'] = 'Status';
$lang['Telephone number'] = 'Telefonnummer';
$lang['There is a new request to download a batch of photos.'] = 'Det finns en ny begäran om att ladda ner ett parti foton.';
$lang['There is a new request to download a set of photos'] = 'Det finns en ny begäran om att ladda ner en uppsättning bilder.';
$lang['There was an error sending your request, please try again'] = 'Det uppstod ett fel när din begäran skulle skickas, försök igen';
$lang['This is used in the email sent when a users download request is accepted.'] = 'Detta används i e-postmeddelandet som skickas när en användares begäran om nedladdning accepteras.';
$lang['This isn\'t a valid email, please try again'] = 'Det här är inte en giltig e-postadress, försök igen';
$lang['This isn\'t the correct format for an email'] = 'Det här är inte rätt format för ett e-postmeddelande';
$lang['User must request permission to download photos'] = 'Användare måste begära tillåtelse att ladda ned foton';
$lang['What are you going to use these photos for?'] = 'Vad ska du använda dessa foton till?';
$lang['You can now <a href="%s">download this set</a>.'] = 'Du kan nu <a href="%s">ladda ned uppsättningen</a>.';
$lang['You did not fill in the required fields correctly, please try again'] = 'Du fyllde inte i de fält som krävs korrekt, försök igen';
$lang['Your download request for the %s has been rejected.'] = 'Din nedladdningsförfrågan för %s har avvisats.';
$lang['Your download request for the set %s has been accepted.'] = 'Din nedladdningsförfrågan för uppsättning %s har accepterats.';
$lang['Your download request has been accepted'] = 'Din nedladdningsförfrågan har accepterats.';
$lang['Your download request has been sent'] = 'Din nedladdningsbegäran har skickats. En administratör kommer att behandla din begäran. Du kommer snart att få ett e-postmeddelande för att ladda ner bilderna.';
$lang['accepted'] = 'accepterad';
$lang['Reason'] = 'Orsak';
$lang['Rejected by'] = 'Avvisats av';
$lang['Request'] = 'Förfrågan';
$lang['Request Date'] = 'Datum för förfrågan';
$lang['Request download'] = 'Nedladdning för förfrågan';
$lang['Request number'] = 'Nummer för förfrågan';
$lang['Request permission to download'] = 'Begär tillåtelse om nedladdning';
$lang['Request permission to download all pictures of this selection'] = 'Begär tillåtelse att ladda ned alla bilder av detta urval';
$lang['Request status'] = 'Status för förfrågan';
$lang['Request to download another size'] = 'Förfrågan om nedladdning av en annan storlek';
$lang['Requests'] = 'Förfrågningar';
$lang['See the request here'] = 'Se förfrågan här';
$lang['Batch downloader, new download request '] = 'Batchnedladdare, ny nedladdningsbegäran';
$lang['Batch downloader, your request has been processed'] = 'Batchnedladdare, din begäran har behandlats';
$lang['Download request'] = 'Nedladdningsförfrågan';
$lang['Download requests'] = 'Nedladdningsförfrågningar';
$lang['Here are the details of the request:'] = 'Här är detaljerna i förfrågan:';
$lang['Here is the link to <a href="%s">our general conditions of use</a>.'] = 'Här är länken till <a href="%s">våra allmänna användningsvillkor</a>.';
$lang['Link to general conditions of use page'] = 'Länk till allmänna användningsvillkor sidan';
$lang['Please choose a photo size for this set'] = 'Välj en fotostorlek för denna uppsättning';
$lang['Please fill out your First name'] = 'Fyll i ditt förnamn';
$lang['Please fill out your Last name'] = 'Fyll i ditt efternamn';
$lang['Please fill out your Organisation'] = 'Fyll i din organisation';
$lang['Please fill out your email'] = 'Fyll i din e-post';
$lang['Please fill out your profession'] = 'Fyll i ditt yrke';
$lang['Please fill out your telephone number'] = 'Fyll i ditt telefonnummer';
$lang['Please give us a reason for your request'] = 'Ange ett skäl för din förfrågan';
$lang['Please provide a first name'] = 'Ange ett förnamn';
$lang['Please provide a last name'] = 'Ange ett efternamn';
$lang['Please provide a reason for using these photos'] = 'Ange ett skäl för att använda dessa foton';
$lang['Please provide an email address'] = 'Ange en e-post adress';
$lang['Profession'] = 'Yrke';
$lang['Email'] = 'E-post';
$lang['First name'] = 'Förnamn';
$lang['For more details or information, please %scontact the administrator%s.'] = 'För mer detaljer eller information, var god %skontakta administratören%s.';
$lang['History'] = 'Historik';
$lang['Last name'] = 'Efternamn';
$lang['Number of photos'] = 'Antal foton';
$lang['Organisation'] = 'Organisation';
$lang['As a reminder, you agree to accept the general conditions of use and to respect the rights relating to intellectual property.'] = 'Som en påminnelse samtycker du till att acceptera de allmänna användarvillkoren och att respektera rättigheterna relaterade till immateriella rättigheter.';
$lang['Accepted by'] = 'Accepterat av';