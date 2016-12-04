<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new crMenu(EWR_MENUBAR_ID); ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(2, "mi_Gensets", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("2", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Gensetssmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(4, "mi_Sites", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("4", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Sitessmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(6, "mi_TopUpp", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("6", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "TopUppsmry.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
