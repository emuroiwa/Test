<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(2, "mmi_Gensets", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("2", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Gensetssmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(4, "mmi_Sites", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("4", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "Sitessmry.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(6, "mmi_TopUpp", $ReportLanguage->Phrase("DetailSummaryReportMenuItemPrefix") . $ReportLanguage->MenuPhrase("6", "MenuText") . $ReportLanguage->Phrase("DetailSummaryReportMenuItemSuffix"), "TopUppsmry.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
