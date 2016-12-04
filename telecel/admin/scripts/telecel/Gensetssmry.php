<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "Gensetssmryinfo.php" ?>
<?php

//
// Page class
//

$Gensets_summary = NULL; // Initialize page object first

class crGensets_summary extends crGensets {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{071D0047-16DB-46CE-AE84-63B7E07DBB14}";

	// Page object name
	var $PageObjName = 'Gensets_summary';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = FALSE;
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $ReportLanguage;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (Gensets)
		if (!isset($GLOBALS["Gensets"])) {
			$GLOBALS["Gensets"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Gensets"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'summary', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'Gensets', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		if (!isset($conn)) $conn = ewr_Connect($this->DBID);

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fGensetssummary";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		$this->name->PlaceHolder = $this->name->FldCaption();
		$this->sitedate->PlaceHolder = $this->sitedate->FldCaption();
		$this->units->PlaceHolder = $this->units->FldCaption();
		$this->rate->PlaceHolder = $this->rate->FldCaption();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $ReportLanguage;
		$exportid = session_id();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = TRUE;
		// Export to Email

		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_Gensets\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_Gensets',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fGensetssummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fGensetssummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fGensetssummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $ReportLanguage, $EWR_EXPORT, $gsExportFile;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			$sContent = ob_get_contents();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				ob_end_clean();
				echo $this->$fn($sContent);
				ewr_CloseConn(); // Close connection
				exit();
			} else {
				$this->$fn($sContent);
			}
		}

		 // Close connection
		ewr_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = 10; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpFldCount = 0;
	var $SubGrpFldCount = 0;
	var $DtlFldCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 5;
		$nGrps = 1;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->name->SelectionList = "";
		$this->name->DefaultSelectionList = "";
		$this->name->ValueList = "";
		$this->sitedate->SelectionList = "";
		$this->sitedate->DefaultSelectionList = "";
		$this->sitedate->ValueList = "";
		$this->units->SelectionList = "";
		$this->units->DefaultSelectionList = "";
		$this->units->ValueList = "";
		$this->rate->SelectionList = "";
		$this->rate->DefaultSelectionList = "";
		$this->rate->ValueList = "";

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Restore filter list
		$this->RestoreFilterList();

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);
		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

		// Get sort
		$this->Sort = $this->GetSort();

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = TRUE;

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
		    $this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup(); 

		// Set no record found message
		if ($this->TotalGrps == 0) {
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);
		$this->SetupFieldCount();
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get count
	function GetCnt($sql) {
		$conn = &$this->Connection();
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row

	//		$rs->MoveFirst(); // NOTE: no need to move position
				$this->FirstRowData = array();
				$this->FirstRowData['name'] = ewr_Conv($rs->fields('name'),200);
				$this->FirstRowData['sitedate'] = ewr_Conv($rs->fields('sitedate'),135);
				$this->FirstRowData['sid'] = ewr_Conv($rs->fields('sid'),3);
				$this->FirstRowData['units'] = ewr_Conv($rs->fields('units'),131);
				$this->FirstRowData['rate'] = ewr_Conv($rs->fields('rate'),131);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->name->setDbValue($rs->fields('name'));
			$this->sitedate->setDbValue($rs->fields('sitedate'));
			$this->sid->setDbValue($rs->fields('sid'));
			$this->units->setDbValue($rs->fields('units'));
			$this->rate->setDbValue($rs->fields('rate'));
			$this->Val[1] = $this->name->CurrentValue;
			$this->Val[2] = $this->sitedate->CurrentValue;
			$this->Val[3] = $this->units->CurrentValue;
			$this->Val[4] = $this->rate->CurrentValue;
		} else {
			$this->name->setDbValue("");
			$this->sitedate->setDbValue("");
			$this->sid->setDbValue("");
			$this->units->setDbValue("");
			$this->rate->setDbValue("");
		}
	}

	//  Set up starting group
	function SetUpStartGroup() {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;

		// Check for a 'start' parameter
		if (@$_GET[EWR_TABLE_START_GROUP] != "") {
			$this->StartGrp = $_GET[EWR_TABLE_START_GROUP];
			$this->setStartGroup($this->StartGrp);
		} elseif (@$_GET["pageno"] != "") {
			$nPageNo = $_GET["pageno"];
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		$conn = &$this->Connection();
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Build distinct values for name

			if ($popupname == 'Gensets_name') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->name, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->name->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->name->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->name->setDbValue($rswrk->fields[0]);
					if (is_null($this->name->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->name->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->name->ViewValue = $this->name->CurrentValue;
						ewr_SetupDistinctValues($this->name->ValueList, $this->name->CurrentValue, $this->name->ViewValue, FALSE, $this->name->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->name->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->name->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->name;
			}

			// Build distinct values for sitedate
			if ($popupname == 'Gensets_sitedate') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->sitedate, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->sitedate->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->sitedate->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->sitedate->setDbValue($rswrk->fields[0]);
					if (is_null($this->sitedate->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->sitedate->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->sitedate->ViewValue = ewr_FormatDateTime($this->sitedate->CurrentValue, 1);
						ewr_SetupDistinctValues($this->sitedate->ValueList, $this->sitedate->CurrentValue, $this->sitedate->ViewValue, FALSE, $this->sitedate->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->sitedate->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->sitedate->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->sitedate;
			}

			// Build distinct values for units
			if ($popupname == 'Gensets_units') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->units, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->units->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->units->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->units->setDbValue($rswrk->fields[0]);
					if (is_null($this->units->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->units->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->units->ViewValue = $this->units->CurrentValue;
						ewr_SetupDistinctValues($this->units->ValueList, $this->units->CurrentValue, $this->units->ViewValue, FALSE, $this->units->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->units->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->units->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->units;
			}

			// Build distinct values for rate
			if ($popupname == 'Gensets_rate') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->rate, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->rate->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->rate->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->rate->setDbValue($rswrk->fields[0]);
					if (is_null($this->rate->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->rate->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->rate->ViewValue = $this->rate->CurrentValue;
						ewr_SetupDistinctValues($this->rate->ValueList, $this->rate->CurrentValue, $this->rate->ViewValue, FALSE, $this->rate->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->rate->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->rate->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->rate;
			}

			// Output data as Json
			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$this->PopupName = $sName;
					if (ewr_IsAdvancedFilterValue($arValues) || $arValues == EWR_INIT_VALUE)
						$this->PopupValue = $arValues;
					if (!ewr_MatchedArray($arValues, $_SESSION["sel_$sName"])) {
						if ($this->HasSessionFilterValues($sName))
							$this->ClearExtFilter = $sName; // Clear extended filter for this field
					}
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ClearSessionSelection('name');
				$this->ClearSessionSelection('sitedate');
				$this->ClearSessionSelection('units');
				$this->ClearSessionSelection('rate');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get name selected values

		if (is_array(@$_SESSION["sel_Gensets_name"])) {
			$this->LoadSelectionFromSession('name');
		} elseif (@$_SESSION["sel_Gensets_name"] == EWR_INIT_VALUE) { // Select all
			$this->name->SelectionList = "";
		}

		// Get sitedate selected values
		if (is_array(@$_SESSION["sel_Gensets_sitedate"])) {
			$this->LoadSelectionFromSession('sitedate');
		} elseif (@$_SESSION["sel_Gensets_sitedate"] == EWR_INIT_VALUE) { // Select all
			$this->sitedate->SelectionList = "";
		}

		// Get units selected values
		if (is_array(@$_SESSION["sel_Gensets_units"])) {
			$this->LoadSelectionFromSession('units');
		} elseif (@$_SESSION["sel_Gensets_units"] == EWR_INIT_VALUE) { // Select all
			$this->units->SelectionList = "";
		}

		// Get rate selected values
		if (is_array(@$_SESSION["sel_Gensets_rate"])) {
			$this->LoadSelectionFromSession('rate');
		} elseif (@$_SESSION["sel_Gensets_rate"] == EWR_INIT_VALUE) { // Select all
			$this->rate->SelectionList = "";
		}
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 10; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 10; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $rs, $Security, $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND && !$this->GrandSummarySetup) { // Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}
		$bGotSummary = TRUE;

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
			$this->RowAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel; // Set up row class

			// name
			$this->name->HrefValue = "";

			// sitedate
			$this->sitedate->HrefValue = "";

			// units
			$this->units->HrefValue = "";

			// rate
			$this->rate->HrefValue = "";
		} else {

			// name
			$this->name->ViewValue = $this->name->CurrentValue;
			$this->name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// sitedate
			$this->sitedate->ViewValue = $this->sitedate->CurrentValue;
			$this->sitedate->ViewValue = ewr_FormatDateTime($this->sitedate->ViewValue, 1);
			$this->sitedate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// units
			$this->units->ViewValue = $this->units->CurrentValue;
			$this->units->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// rate
			$this->rate->ViewValue = $this->rate->CurrentValue;
			$this->rate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// name
			$this->name->HrefValue = "";

			// sitedate
			$this->sitedate->HrefValue = "";

			// units
			$this->units->HrefValue = "";

			// rate
			$this->rate->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// name
			$CurrentValue = $this->name->CurrentValue;
			$ViewValue = &$this->name->ViewValue;
			$ViewAttrs = &$this->name->ViewAttrs;
			$CellAttrs = &$this->name->CellAttrs;
			$HrefValue = &$this->name->HrefValue;
			$LinkAttrs = &$this->name->LinkAttrs;
			$this->Cell_Rendered($this->name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// sitedate
			$CurrentValue = $this->sitedate->CurrentValue;
			$ViewValue = &$this->sitedate->ViewValue;
			$ViewAttrs = &$this->sitedate->ViewAttrs;
			$CellAttrs = &$this->sitedate->CellAttrs;
			$HrefValue = &$this->sitedate->HrefValue;
			$LinkAttrs = &$this->sitedate->LinkAttrs;
			$this->Cell_Rendered($this->sitedate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// units
			$CurrentValue = $this->units->CurrentValue;
			$ViewValue = &$this->units->ViewValue;
			$ViewAttrs = &$this->units->ViewAttrs;
			$CellAttrs = &$this->units->CellAttrs;
			$HrefValue = &$this->units->HrefValue;
			$LinkAttrs = &$this->units->LinkAttrs;
			$this->Cell_Rendered($this->units, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// rate
			$CurrentValue = $this->rate->CurrentValue;
			$ViewValue = &$this->rate->ViewValue;
			$ViewAttrs = &$this->rate->ViewAttrs;
			$CellAttrs = &$this->rate->CellAttrs;
			$HrefValue = &$this->rate->HrefValue;
			$LinkAttrs = &$this->rate->LinkAttrs;
			$this->Cell_Rendered($this->rate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpFldCount = 0;
		$this->SubGrpFldCount = 0;
		$this->DtlFldCount = 0;
		if ($this->name->Visible) $this->DtlFldCount += 1;
		if ($this->sitedate->Visible) $this->DtlFldCount += 1;
		if ($this->units->Visible) $this->DtlFldCount += 1;
		if ($this->rate->Visible) $this->DtlFldCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("summary", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage;
	}

	// Return extended filter
	function GetExtendedFilter() {
		global $gsFormError;
		$sFilter = "";
		if ($this->DrillDown)
			return "";
		$bPostBack = ewr_IsHttpPost();
		$bRestoreSession = TRUE;
		$bSetupFilter = FALSE;

		// Reset extended filter if filter changed
		if ($bPostBack) {

			// Clear extended filter for field name
			if ($this->ClearExtFilter == 'Gensets_name')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'name');

			// Clear extended filter for field sitedate
			if ($this->ClearExtFilter == 'Gensets_sitedate')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'sitedate');

			// Clear extended filter for field units
			if ($this->ClearExtFilter == 'Gensets_units')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'units');

			// Clear extended filter for field rate
			if ($this->ClearExtFilter == 'Gensets_rate')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'rate');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->name->SearchValue, $this->name->SearchOperator, $this->name->SearchCondition, $this->name->SearchValue2, $this->name->SearchOperator2, 'name'); // Field name
			$this->SetSessionFilterValues($this->sitedate->SearchValue, $this->sitedate->SearchOperator, $this->sitedate->SearchCondition, $this->sitedate->SearchValue2, $this->sitedate->SearchOperator2, 'sitedate'); // Field sitedate
			$this->SetSessionFilterValues($this->units->SearchValue, $this->units->SearchOperator, $this->units->SearchCondition, $this->units->SearchValue2, $this->units->SearchOperator2, 'units'); // Field units
			$this->SetSessionFilterValues($this->rate->SearchValue, $this->rate->SearchOperator, $this->rate->SearchCondition, $this->rate->SearchValue2, $this->rate->SearchOperator2, 'rate'); // Field rate

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field name
			if ($this->GetFilterValues($this->name)) {
				$bSetupFilter = TRUE;
			}

			// Field sitedate
			if ($this->GetFilterValues($this->sitedate)) {
				$bSetupFilter = TRUE;
			}

			// Field units
			if ($this->GetFilterValues($this->units)) {
				$bSetupFilter = TRUE;
			}

			// Field rate
			if ($this->GetFilterValues($this->rate)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->name); // Field name
			$this->GetSessionFilterValues($this->sitedate); // Field sitedate
			$this->GetSessionFilterValues($this->units); // Field units
			$this->GetSessionFilterValues($this->rate); // Field rate
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->name, $sFilter, FALSE, TRUE); // Field name
		$this->BuildExtendedFilter($this->sitedate, $sFilter, FALSE, TRUE); // Field sitedate
		$this->BuildExtendedFilter($this->units, $sFilter, FALSE, TRUE); // Field units
		$this->BuildExtendedFilter($this->rate, $sFilter, FALSE, TRUE); // Field rate

		// Save parms to session
		$this->SetSessionFilterValues($this->name->SearchValue, $this->name->SearchOperator, $this->name->SearchCondition, $this->name->SearchValue2, $this->name->SearchOperator2, 'name'); // Field name
		$this->SetSessionFilterValues($this->sitedate->SearchValue, $this->sitedate->SearchOperator, $this->sitedate->SearchCondition, $this->sitedate->SearchValue2, $this->sitedate->SearchOperator2, 'sitedate'); // Field sitedate
		$this->SetSessionFilterValues($this->units->SearchValue, $this->units->SearchOperator, $this->units->SearchCondition, $this->units->SearchValue2, $this->units->SearchOperator2, 'units'); // Field units
		$this->SetSessionFilterValues($this->rate->SearchValue, $this->rate->SearchOperator, $this->rate->SearchCondition, $this->rate->SearchValue2, $this->rate->SearchOperator2, 'rate'); // Field rate

		// Setup filter
		if ($bSetupFilter) {

			// Field name
			$sWrk = "";
			$this->BuildExtendedFilter($this->name, $sWrk);
			ewr_LoadSelectionFromFilter($this->name, $sWrk, $this->name->SelectionList);
			$_SESSION['sel_Gensets_name'] = ($this->name->SelectionList == "") ? EWR_INIT_VALUE : $this->name->SelectionList;

			// Field sitedate
			$sWrk = "";
			$this->BuildExtendedFilter($this->sitedate, $sWrk);
			ewr_LoadSelectionFromFilter($this->sitedate, $sWrk, $this->sitedate->SelectionList);
			$_SESSION['sel_Gensets_sitedate'] = ($this->sitedate->SelectionList == "") ? EWR_INIT_VALUE : $this->sitedate->SelectionList;

			// Field units
			$sWrk = "";
			$this->BuildExtendedFilter($this->units, $sWrk);
			ewr_LoadSelectionFromFilter($this->units, $sWrk, $this->units->SelectionList);
			$_SESSION['sel_Gensets_units'] = ($this->units->SelectionList == "") ? EWR_INIT_VALUE : $this->units->SelectionList;

			// Field rate
			$sWrk = "";
			$this->BuildExtendedFilter($this->rate, $sWrk);
			ewr_LoadSelectionFromFilter($this->rate, $sWrk, $this->rate->SelectionList);
			$_SESSION['sel_Gensets_rate'] = ($this->rate->SelectionList == "") ? EWR_INIT_VALUE : $this->rate->SelectionList;
		}
		return $sFilter;
	}

	// Build dropdown filter
	function BuildDropDownFilter(&$fld, &$FilterClause, $FldOpr, $Default = FALSE, $SaveFilter = FALSE) {
		$FldVal = ($Default) ? $fld->DefaultDropDownValue : $fld->DropDownValue;
		$sSql = "";
		if (is_array($FldVal)) {
			foreach ($FldVal as $val) {
				$sWrk = $this->GetDropDownFilter($fld, $val, $FldOpr);

				// Call Page Filtering event
				if (substr($val, 0, 2) <> "@@") $this->Page_Filtering($fld, $sWrk, "dropdown", $FldOpr, $val);
				if ($sWrk <> "") {
					if ($sSql <> "")
						$sSql .= " OR " . $sWrk;
					else
						$sSql = $sWrk;
				}
			}
		} else {
			$sSql = $this->GetDropDownFilter($fld, $FldVal, $FldOpr);

			// Call Page Filtering event
			if (substr($FldVal, 0, 2) <> "@@") $this->Page_Filtering($fld, $sSql, "dropdown", $FldOpr, $FldVal);
		}
		if ($sSql <> "") {
			ewr_AddFilter($FilterClause, $sSql);
			if ($SaveFilter) $fld->CurrentFilter = $sSql;
		}
	}

	function GetDropDownFilter(&$fld, $FldVal, $FldOpr) {
		$FldName = $fld->FldName;
		$FldExpression = $fld->FldExpression;
		$FldDataType = $fld->FldDataType;
		$FldDelimiter = $fld->FldDelimiter;
		$FldVal = strval($FldVal);
		if ($FldOpr == "") $FldOpr = "=";
		$sWrk = "";
		if ($FldVal == EWR_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NULL";
		} elseif ($FldVal == EWR_NOT_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NOT NULL";
		} elseif ($FldVal == EWR_EMPTY_VALUE) {
			$sWrk = $FldExpression . " = ''";
		} elseif ($FldVal == EWR_ALL_VALUE) {
			$sWrk = "1 = 1";
		} else {
			if (substr($FldVal, 0, 2) == "@@") {
				$sWrk = $this->GetCustomFilter($fld, $FldVal);
			} elseif ($FldDelimiter <> "" && trim($FldVal) <> "") {
				$sWrk = ewr_GetMultiSearchSql($FldExpression, trim($FldVal), $this->DBID);
			} else {
				if ($FldVal <> "" && $FldVal <> EWR_INIT_VALUE) {
					if ($FldDataType == EWR_DATATYPE_DATE && $FldOpr <> "") {
						$sWrk = ewr_DateFilterString($FldExpression, $FldOpr, $FldVal, $FldDataType, $this->DBID);
					} else {
						$sWrk = ewr_FilterString($FldOpr, $FldVal, $FldDataType, $this->DBID);
						if ($sWrk <> "") $sWrk = $FldExpression . $sWrk;
					}
				}
			}
		}
		return $sWrk;
	}

	// Get custom filter
	function GetCustomFilter(&$fld, $FldVal) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld);
					else
						$sWrk = "";
					$this->Page_Filtering($fld, $sWrk, "custom", $wrkid);
					break;
				}
			}
		}
		return $sWrk;
	}

	// Build extended filter
	function BuildExtendedFilter(&$fld, &$FilterClause, $Default = FALSE, $SaveFilter = FALSE) {
		$sWrk = ewr_GetExtendedFilter($fld, $Default, $this->DBID);
		if (!$Default)
			$this->Page_Filtering($fld, $sWrk, "extended", $fld->SearchOperator, $fld->SearchValue, $fld->SearchCondition, $fld->SearchOperator2, $fld->SearchValue2);
		if ($sWrk <> "") {
			ewr_AddFilter($FilterClause, $sWrk);
			if ($SaveFilter) $fld->CurrentFilter = $sWrk;
		}
	}

	// Get drop down value from querystring
	function GetDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return FALSE; // Skip post back
		if (isset($_GET["so_$parm"]))
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
		if (isset($_GET["sv_$parm"])) {
			$fld->DropDownValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			return TRUE;
		}
		return FALSE;
	}

	// Get filter values from querystring
	function GetFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return; // Skip post back
		$got = FALSE;
		if (isset($_GET["sv_$parm"])) {
			$fld->SearchValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so_$parm"])) {
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sc_$parm"])) {
			$fld->SearchCondition = ewr_StripSlashes(@$_GET["sc_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sv2_$parm"])) {
			$fld->SearchValue2 = ewr_StripSlashes(@$_GET["sv2_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so2_$parm"])) {
			$fld->SearchOperator2 = ewr_StripSlashes($_GET["so2_$parm"]);
			$got = TRUE;
		}
		return $got;
	}

	// Set default ext filter
	function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2) {
		$fld->DefaultSearchValue = $sv1; // Default ext filter value 1
		$fld->DefaultSearchValue2 = $sv2; // Default ext filter value 2 (if operator 2 is enabled)
		$fld->DefaultSearchOperator = $so1; // Default search operator 1
		$fld->DefaultSearchOperator2 = $so2; // Default search operator 2 (if operator 2 is enabled)
		$fld->DefaultSearchCondition = $sc; // Default search condition (if operator 2 is enabled)
	}

	// Apply default ext filter
	function ApplyDefaultExtFilter(&$fld) {
		$fld->SearchValue = $fld->DefaultSearchValue;
		$fld->SearchValue2 = $fld->DefaultSearchValue2;
		$fld->SearchOperator = $fld->DefaultSearchOperator;
		$fld->SearchOperator2 = $fld->DefaultSearchOperator2;
		$fld->SearchCondition = $fld->DefaultSearchCondition;
	}

	// Check if Text Filter applied
	function TextFilterApplied(&$fld) {
		return (strval($fld->SearchValue) <> strval($fld->DefaultSearchValue) ||
			strval($fld->SearchValue2) <> strval($fld->DefaultSearchValue2) ||
			(strval($fld->SearchValue) <> "" &&
				strval($fld->SearchOperator) <> strval($fld->DefaultSearchOperator)) ||
			(strval($fld->SearchValue2) <> "" &&
				strval($fld->SearchOperator2) <> strval($fld->DefaultSearchOperator2)) ||
			strval($fld->SearchCondition) <> strval($fld->DefaultSearchCondition));
	}

	// Check if Non-Text Filter applied
	function NonTextFilterApplied(&$fld) {
		if (is_array($fld->DropDownValue)) {
			if (is_array($fld->DefaultDropDownValue)) {
				if (count($fld->DefaultDropDownValue) <> count($fld->DropDownValue))
					return TRUE;
				else
					return (count(array_diff($fld->DefaultDropDownValue, $fld->DropDownValue)) <> 0);
			} else {
				return TRUE;
			}
		} else {
			if (is_array($fld->DefaultDropDownValue))
				return TRUE;
			else
				$v1 = strval($fld->DefaultDropDownValue);
			if ($v1 == EWR_INIT_VALUE)
				$v1 = "";
			$v2 = strval($fld->DropDownValue);
			if ($v2 == EWR_INIT_VALUE || $v2 == EWR_ALL_VALUE)
				$v2 = "";
			return ($v1 <> $v2);
		}
	}

	// Get dropdown value from session
	function GetSessionDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->DropDownValue, 'sv_Gensets_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Gensets_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_Gensets_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_Gensets_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_Gensets_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_Gensets_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_Gensets_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_Gensets_' . $parm] = $sv;
		$_SESSION['so_Gensets_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_Gensets_' . $parm] = $sv1;
		$_SESSION['so_Gensets_' . $parm] = $so1;
		$_SESSION['sc_Gensets_' . $parm] = $sc;
		$_SESSION['sv2_Gensets_' . $parm] = $sv2;
		$_SESSION['so2_Gensets_' . $parm] = $so2;
	}

	// Check if has Session filter values
	function HasSessionFilterValues($parm) {
		return ((@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv2_' . $parm] <> "" && @$_SESSION['sv2_' . $parm] <> EWR_INIT_VALUE));
	}

	// Dropdown filter exist
	function DropDownFilterExist(&$fld, $FldOpr) {
		$sWrk = "";
		$this->BuildDropDownFilter($fld, $sWrk, $FldOpr);
		return ($sWrk <> "");
	}

	// Extended filter exist
	function ExtendedFilterExist(&$fld) {
		$sExtWrk = "";
		$this->BuildExtendedFilter($fld, $sExtWrk);
		return ($sExtWrk <> "");
	}

	// Validate form
	function ValidateForm() {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!ewr_CheckDate($this->sitedate->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->sitedate->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->units->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->units->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->rate->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->rate->FldErrMsg();
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			$gsFormError .= ($gsFormError <> "") ? "<p>&nbsp;</p>" : "";
			$gsFormError .= $sFormCustomError;
		}
		return $ValidateForm;
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_Gensets_$parm"] = "";
		$_SESSION["rf_Gensets_$parm"] = "";
		$_SESSION["rt_Gensets_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_Gensets_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_Gensets_$parm"];
		$fld->RangeTo = @$_SESSION["rt_Gensets_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {

		/**
		* Set up default values for non Text filters
		*/

		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/

		// Field name
		$this->SetDefaultExtFilter($this->name, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->name);
		$sWrk = "";
		$this->BuildExtendedFilter($this->name, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->name, $sWrk, $this->name->DefaultSelectionList);
		if (!$this->SearchCommand) $this->name->SelectionList = $this->name->DefaultSelectionList;

		// Field sitedate
		$this->SetDefaultExtFilter($this->sitedate, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->sitedate);
		$sWrk = "";
		$this->BuildExtendedFilter($this->sitedate, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->sitedate, $sWrk, $this->sitedate->DefaultSelectionList);
		if (!$this->SearchCommand) $this->sitedate->SelectionList = $this->sitedate->DefaultSelectionList;

		// Field units
		$this->SetDefaultExtFilter($this->units, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->units);
		$sWrk = "";
		$this->BuildExtendedFilter($this->units, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->units, $sWrk, $this->units->DefaultSelectionList);
		if (!$this->SearchCommand) $this->units->SelectionList = $this->units->DefaultSelectionList;

		// Field rate
		$this->SetDefaultExtFilter($this->rate, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->rate);
		$sWrk = "";
		$this->BuildExtendedFilter($this->rate, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->rate, $sWrk, $this->rate->DefaultSelectionList);
		if (!$this->SearchCommand) $this->rate->SelectionList = $this->rate->DefaultSelectionList;

		/**
		* Set up default values for popup filters
		*/

		// Field name
		// $this->name->DefaultSelectionList = array("val1", "val2");
		// Field sitedate
		// $this->sitedate->DefaultSelectionList = array("val1", "val2");
		// Field units
		// $this->units->DefaultSelectionList = array("val1", "val2");
		// Field rate
		// $this->rate->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check name text filter
		if ($this->TextFilterApplied($this->name))
			return TRUE;

		// Check name popup filter
		if (!ewr_MatchedArray($this->name->DefaultSelectionList, $this->name->SelectionList))
			return TRUE;

		// Check sitedate text filter
		if ($this->TextFilterApplied($this->sitedate))
			return TRUE;

		// Check sitedate popup filter
		if (!ewr_MatchedArray($this->sitedate->DefaultSelectionList, $this->sitedate->SelectionList))
			return TRUE;

		// Check units text filter
		if ($this->TextFilterApplied($this->units))
			return TRUE;

		// Check units popup filter
		if (!ewr_MatchedArray($this->units->DefaultSelectionList, $this->units->SelectionList))
			return TRUE;

		// Check rate text filter
		if ($this->TextFilterApplied($this->rate))
			return TRUE;

		// Check rate popup filter
		if (!ewr_MatchedArray($this->rate->DefaultSelectionList, $this->rate->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field name
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->name, $sExtWrk);
		if (is_array($this->name->SelectionList))
			$sWrk = ewr_JoinArray($this->name->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->name->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field sitedate
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->sitedate, $sExtWrk);
		if (is_array($this->sitedate->SelectionList))
			$sWrk = ewr_JoinArray($this->sitedate->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->sitedate->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field units
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->units, $sExtWrk);
		if (is_array($this->units->SelectionList))
			$sWrk = ewr_JoinArray($this->units->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->units->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field rate
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->rate, $sExtWrk);
		if (is_array($this->rate->SelectionList))
			$sWrk = ewr_JoinArray($this->rate->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->rate->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = "";
		$divdataclass = "";

		// Show Filters
		if ($sFilterList <> "") {
			$sMessage = "<div class=\"ewDisplayTable\"" . $divstyle . "><div id=\"ewrFilterList\" class=\"alert alert-info\"" . $divdataclass . "><div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList . "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		}
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

		// Field name
		$sWrk = "";
		if ($this->name->SearchValue <> "" || $this->name->SearchValue2 <> "") {
			$sWrk = "\"sv_name\":\"" . ewr_JsEncode2($this->name->SearchValue) . "\"," .
				"\"so_name\":\"" . ewr_JsEncode2($this->name->SearchOperator) . "\"," .
				"\"sc_name\":\"" . ewr_JsEncode2($this->name->SearchCondition) . "\"," .
				"\"sv2_name\":\"" . ewr_JsEncode2($this->name->SearchValue2) . "\"," .
				"\"so2_name\":\"" . ewr_JsEncode2($this->name->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->name->SelectionList <> EWR_INIT_VALUE) ? $this->name->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_name\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field sitedate
		$sWrk = "";
		if ($this->sitedate->SearchValue <> "" || $this->sitedate->SearchValue2 <> "") {
			$sWrk = "\"sv_sitedate\":\"" . ewr_JsEncode2($this->sitedate->SearchValue) . "\"," .
				"\"so_sitedate\":\"" . ewr_JsEncode2($this->sitedate->SearchOperator) . "\"," .
				"\"sc_sitedate\":\"" . ewr_JsEncode2($this->sitedate->SearchCondition) . "\"," .
				"\"sv2_sitedate\":\"" . ewr_JsEncode2($this->sitedate->SearchValue2) . "\"," .
				"\"so2_sitedate\":\"" . ewr_JsEncode2($this->sitedate->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->sitedate->SelectionList <> EWR_INIT_VALUE) ? $this->sitedate->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_sitedate\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field units
		$sWrk = "";
		if ($this->units->SearchValue <> "" || $this->units->SearchValue2 <> "") {
			$sWrk = "\"sv_units\":\"" . ewr_JsEncode2($this->units->SearchValue) . "\"," .
				"\"so_units\":\"" . ewr_JsEncode2($this->units->SearchOperator) . "\"," .
				"\"sc_units\":\"" . ewr_JsEncode2($this->units->SearchCondition) . "\"," .
				"\"sv2_units\":\"" . ewr_JsEncode2($this->units->SearchValue2) . "\"," .
				"\"so2_units\":\"" . ewr_JsEncode2($this->units->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->units->SelectionList <> EWR_INIT_VALUE) ? $this->units->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_units\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field rate
		$sWrk = "";
		if ($this->rate->SearchValue <> "" || $this->rate->SearchValue2 <> "") {
			$sWrk = "\"sv_rate\":\"" . ewr_JsEncode2($this->rate->SearchValue) . "\"," .
				"\"so_rate\":\"" . ewr_JsEncode2($this->rate->SearchOperator) . "\"," .
				"\"sc_rate\":\"" . ewr_JsEncode2($this->rate->SearchCondition) . "\"," .
				"\"sv2_rate\":\"" . ewr_JsEncode2($this->rate->SearchValue2) . "\"," .
				"\"so2_rate\":\"" . ewr_JsEncode2($this->rate->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->rate->SelectionList <> EWR_INIT_VALUE) ? $this->rate->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_rate\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);

		// Field name
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_name", $filter) || array_key_exists("so_name", $filter) ||
			array_key_exists("sc_name", $filter) ||
			array_key_exists("sv2_name", $filter) || array_key_exists("so2_name", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_name"], @$filter["so_name"], @$filter["sc_name"], @$filter["sv2_name"], @$filter["so2_name"], "name");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_name", $filter)) {
			$sWrk = $filter["sel_name"];
			$sWrk = explode("||", $sWrk);
			$this->name->SelectionList = $sWrk;
			$_SESSION["sel_Gensets_name"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "name"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "name");
			$this->name->SelectionList = "";
			$_SESSION["sel_Gensets_name"] = "";
		}

		// Field sitedate
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_sitedate", $filter) || array_key_exists("so_sitedate", $filter) ||
			array_key_exists("sc_sitedate", $filter) ||
			array_key_exists("sv2_sitedate", $filter) || array_key_exists("so2_sitedate", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_sitedate"], @$filter["so_sitedate"], @$filter["sc_sitedate"], @$filter["sv2_sitedate"], @$filter["so2_sitedate"], "sitedate");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_sitedate", $filter)) {
			$sWrk = $filter["sel_sitedate"];
			$sWrk = explode("||", $sWrk);
			$this->sitedate->SelectionList = $sWrk;
			$_SESSION["sel_Gensets_sitedate"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "sitedate"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "sitedate");
			$this->sitedate->SelectionList = "";
			$_SESSION["sel_Gensets_sitedate"] = "";
		}

		// Field units
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_units", $filter) || array_key_exists("so_units", $filter) ||
			array_key_exists("sc_units", $filter) ||
			array_key_exists("sv2_units", $filter) || array_key_exists("so2_units", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_units"], @$filter["so_units"], @$filter["sc_units"], @$filter["sv2_units"], @$filter["so2_units"], "units");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_units", $filter)) {
			$sWrk = $filter["sel_units"];
			$sWrk = explode("||", $sWrk);
			$this->units->SelectionList = $sWrk;
			$_SESSION["sel_Gensets_units"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "units"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "units");
			$this->units->SelectionList = "";
			$_SESSION["sel_Gensets_units"] = "";
		}

		// Field rate
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_rate", $filter) || array_key_exists("so_rate", $filter) ||
			array_key_exists("sc_rate", $filter) ||
			array_key_exists("sv2_rate", $filter) || array_key_exists("so2_rate", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_rate"], @$filter["so_rate"], @$filter["sc_rate"], @$filter["sv2_rate"], @$filter["so2_rate"], "rate");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_rate", $filter)) {
			$sWrk = $filter["sel_rate"];
			$sWrk = explode("||", $sWrk);
			$this->rate->SelectionList = $sWrk;
			$_SESSION["sel_Gensets_rate"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "rate"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "rate");
			$this->rate->SelectionList = "";
			$_SESSION["sel_Gensets_rate"] = "";
		}
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->name)) {
			if (is_array($this->name->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->name, "`name`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->name, $sFilter, "popup");
				$this->name->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->sitedate)) {
			if (is_array($this->sitedate->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->sitedate, "`sitedate`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->sitedate, $sFilter, "popup");
				$this->sitedate->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->units)) {
			if (is_array($this->units->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->units, "`units`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->units, $sFilter, "popup");
				$this->units->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->rate)) {
			if (is_array($this->rate->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->rate, "`rate`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->rate, $sFilter, "popup");
				$this->rate->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort() {
		if ($this->DrillDown)
			return "";

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
				$this->name->setSort("");
				$this->sitedate->setSort("");
				$this->units->setSort("");
				$this->rate->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export to HTML
	function ExportHtml($html) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');
		//echo $html;

	} 

	// Export to WORD
	function ExportWord($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
		echo $html;
	}

	// Export to EXCEL
	function ExportExcel($html) {
		global $gsExportFile;
		header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
		echo $html;
	}

	// Export to PDF
	function ExportPdf($html) {
		ob_end_clean();
		echo($html);
		ewr_DeleteTmpImages($html);
		exit();
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Gensets_summary)) $Gensets_summary = new crGensets_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$Gensets_summary;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "" || $Page->Export == "print" || $Page->Export == "email" && @$gsEmailContentType == "url") { ?>
<script type="text/javascript">

// Create page object
var Gensets_summary = new ewr_Page("Gensets_summary");

// Page properties
Gensets_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = Gensets_summary.PageID;

// Extend page with Chart_Rendering function
Gensets_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
Gensets_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fGensetssummary = new ewr_Form("fGensetssummary");

// Validate method
fGensetssummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_sitedate;
	if (elm && !ewr_CheckDate(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->sitedate->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_units;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->units->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_rate;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->rate->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fGensetssummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fGensetssummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fGensetssummary.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Use Ajax
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- summary report starts -->
<?php if ($Page->Export <> "pdf") { ?>
<div id="report_summary">
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fGensetssummary" id="fGensetssummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fGensetssummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_name" class="ewCell form-group">
	<label for="sv_name" class="ewSearchCaption ewLabel"><?php echo $Page->name->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_name" id="so_name" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->name->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->name->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->name->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->name->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->name->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->name->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->name->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->name->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->name->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->name->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->name->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->name->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->name->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->name->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_name" id="sv_name" name="sv_name" size="30" maxlength="111" placeholder="<?php echo $Page->name->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->name->SearchValue) ?>"<?php echo $Page->name->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_name" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_name" style="display: none">
<?php ewr_PrependClass($Page->name->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_name" id="sv2_name" name="sv2_name" size="30" maxlength="111" placeholder="<?php echo $Page->name->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->name->SearchValue2) ?>"<?php echo $Page->name->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_sitedate" class="ewCell form-group">
	<label for="sv_sitedate" class="ewSearchCaption ewLabel"><?php echo $Page->sitedate->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_sitedate" id="so_sitedate" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->sitedate->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->sitedate->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->sitedate->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->sitedate->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->sitedate->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->sitedate->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->sitedate->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->sitedate->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->sitedate->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->sitedate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_sitedate" id="sv_sitedate" name="sv_sitedate" placeholder="<?php echo $Page->sitedate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->sitedate->SearchValue) ?>"<?php echo $Page->sitedate->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_sitedate" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_sitedate" style="display: none">
<?php ewr_PrependClass($Page->sitedate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_sitedate" id="sv2_sitedate" name="sv2_sitedate" placeholder="<?php echo $Page->sitedate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->sitedate->SearchValue2) ?>"<?php echo $Page->sitedate->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_units" class="ewCell form-group">
	<label for="sv_units" class="ewSearchCaption ewLabel"><?php echo $Page->units->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_units" id="so_units" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->units->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->units->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->units->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->units->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->units->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->units->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->units->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->units->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->units->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->units->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_units" id="sv_units" name="sv_units" size="30" placeholder="<?php echo $Page->units->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->units->SearchValue) ?>"<?php echo $Page->units->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_units" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_units" style="display: none">
<?php ewr_PrependClass($Page->units->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_units" id="sv2_units" name="sv2_units" size="30" placeholder="<?php echo $Page->units->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->units->SearchValue2) ?>"<?php echo $Page->units->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_rate" class="ewCell form-group">
	<label for="sv_rate" class="ewSearchCaption ewLabel"><?php echo $Page->rate->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_rate" id="so_rate" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->rate->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->rate->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->rate->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->rate->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->rate->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->rate->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->rate->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->rate->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->rate->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->rate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_rate" id="sv_rate" name="sv_rate" size="30" placeholder="<?php echo $Page->rate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->rate->SearchValue) ?>"<?php echo $Page->rate->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_rate" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_rate" style="display: none">
<?php ewr_PrependClass($Page->rate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="Gensets" data-field="x_rate" id="sv2_rate" name="sv2_rate" size="30" placeholder="<?php echo $Page->rate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->rate->SearchValue2) ?>"<?php echo $Page->rate->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fGensetssummary.Init();
fGensetssummary.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
<?php } ?>
<?php } ?>
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray(2, -1);
$Page->GrpIdx[0] = -1;
$Page->GrpIdx[1] = $Page->StopGrp - $Page->StartGrp + 1;
while ($rs && !$rs->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Gensetssmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="name"><div class="Gensets_name"><span class="ewTableHeaderCaption"><?php echo $Page->name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="name">
<?php if ($Page->SortUrl($Page->name) == "") { ?>
		<div class="ewTableHeaderBtn Gensets_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->name->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_name', false, '<?php echo $Page->name->RangeFrom; ?>', '<?php echo $Page->name->RangeTo; ?>');" id="x_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Gensets_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_name', false, '<?php echo $Page->name->RangeFrom; ?>', '<?php echo $Page->name->RangeTo; ?>');" id="x_name<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->sitedate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="sitedate"><div class="Gensets_sitedate"><span class="ewTableHeaderCaption"><?php echo $Page->sitedate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="sitedate">
<?php if ($Page->SortUrl($Page->sitedate) == "") { ?>
		<div class="ewTableHeaderBtn Gensets_sitedate">
			<span class="ewTableHeaderCaption"><?php echo $Page->sitedate->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_sitedate', false, '<?php echo $Page->sitedate->RangeFrom; ?>', '<?php echo $Page->sitedate->RangeTo; ?>');" id="x_sitedate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Gensets_sitedate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->sitedate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->sitedate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->sitedate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->sitedate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_sitedate', false, '<?php echo $Page->sitedate->RangeFrom; ?>', '<?php echo $Page->sitedate->RangeTo; ?>');" id="x_sitedate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->units->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="units"><div class="Gensets_units"><span class="ewTableHeaderCaption"><?php echo $Page->units->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="units">
<?php if ($Page->SortUrl($Page->units) == "") { ?>
		<div class="ewTableHeaderBtn Gensets_units">
			<span class="ewTableHeaderCaption"><?php echo $Page->units->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_units', false, '<?php echo $Page->units->RangeFrom; ?>', '<?php echo $Page->units->RangeTo; ?>');" id="x_units<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Gensets_units" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->units) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->units->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->units->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->units->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_units', false, '<?php echo $Page->units->RangeFrom; ?>', '<?php echo $Page->units->RangeTo; ?>');" id="x_units<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->rate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="rate"><div class="Gensets_rate"><span class="ewTableHeaderCaption"><?php echo $Page->rate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="rate">
<?php if ($Page->SortUrl($Page->rate) == "") { ?>
		<div class="ewTableHeaderBtn Gensets_rate">
			<span class="ewTableHeaderCaption"><?php echo $Page->rate->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_rate', false, '<?php echo $Page->rate->RangeFrom; ?>', '<?php echo $Page->rate->RangeTo; ?>');" id="x_rate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer Gensets_rate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->rate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->rate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->rate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->rate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'Gensets_rate', false, '<?php echo $Page->rate->RangeFrom; ?>', '<?php echo $Page->rate->RangeTo; ?>');" id="x_rate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}
	$Page->RecCount++;
	$Page->RecIndex++;

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->name->Visible) { ?>
		<td data-field="name"<?php echo $Page->name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Gensets_name"<?php echo $Page->name->ViewAttributes() ?>><?php echo $Page->name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->sitedate->Visible) { ?>
		<td data-field="sitedate"<?php echo $Page->sitedate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Gensets_sitedate"<?php echo $Page->sitedate->ViewAttributes() ?>><?php echo $Page->sitedate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->units->Visible) { ?>
		<td data-field="units"<?php echo $Page->units->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Gensets_units"<?php echo $Page->units->ViewAttributes() ?>><?php echo $Page->units->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->rate->Visible) { ?>
		<td data-field="rate"<?php echo $Page->rate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_Gensets_rate"<?php echo $Page->rate->ViewAttributes() ?>><?php echo $Page->rate->ListViewValue() ?></span></td>
<?php } ?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
	$Page->GrpCount++;
} // End while
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
<?php
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>><td colspan="<?php echo ($Page->GrpFldCount + $Page->DtlFldCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandSummary") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "Gensetssmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || TRUE) { // Show footer ?>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->TotalGrps > 0) { ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "Gensetssmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
