<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg9.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn9.php" ?>
<?php include_once "phprptinc/ewrusrfn9.php" ?>
<?php include_once "TopUppsmryinfo.php" ?>
<?php

//
// Page class
//

$TopUpp_summary = NULL; // Initialize page object first

class crTopUpp_summary extends crTopUpp {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{071D0047-16DB-46CE-AE84-63B7E07DBB14}";

	// Page object name
	var $PageObjName = 'TopUpp_summary';

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

		// Table object (TopUpp)
		if (!isset($GLOBALS["TopUpp"])) {
			$GLOBALS["TopUpp"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["TopUpp"];
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
			define("EWR_TABLE_NAME", 'TopUpp', TRUE);

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
		$this->FilterOptions->TagClassName = "ewFilterOption fTopUppsummary";
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
		$this->oldlevel->PlaceHolder = $this->oldlevel->FldCaption();
		$this->topuplevel->PlaceHolder = $this->topuplevel->FldCaption();
		$this->topupdate->PlaceHolder = $this->topupdate->FldCaption();
		$this->item->PlaceHolder = $this->item->FldCaption();
		$this->type->PlaceHolder = $this->type->FldCaption();

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
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_TopUpp\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_TopUpp',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
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
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fTopUppsummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
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
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fTopUppsummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fTopUppsummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
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

		$nDtls = 6;
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
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->oldlevel->SelectionList = "";
		$this->oldlevel->DefaultSelectionList = "";
		$this->oldlevel->ValueList = "";
		$this->topuplevel->SelectionList = "";
		$this->topuplevel->DefaultSelectionList = "";
		$this->topuplevel->ValueList = "";
		$this->topupdate->SelectionList = "";
		$this->topupdate->DefaultSelectionList = "";
		$this->topupdate->ValueList = "";
		$this->item->SelectionList = "";
		$this->item->DefaultSelectionList = "";
		$this->item->ValueList = "";
		$this->type->SelectionList = "";
		$this->type->DefaultSelectionList = "";
		$this->type->ValueList = "";

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
				$this->FirstRowData['oldlevel'] = ewr_Conv($rs->fields('oldlevel'),131);
				$this->FirstRowData['topuplevel'] = ewr_Conv($rs->fields('topuplevel'),131);
				$this->FirstRowData['topupdate'] = ewr_Conv($rs->fields('topupdate'),135);
				$this->FirstRowData['item'] = ewr_Conv($rs->fields('item'),200);
				$this->FirstRowData['tid'] = ewr_Conv($rs->fields('tid'),3);
				$this->FirstRowData['type'] = ewr_Conv($rs->fields('type'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->oldlevel->setDbValue($rs->fields('oldlevel'));
			$this->topuplevel->setDbValue($rs->fields('topuplevel'));
			$this->topupdate->setDbValue($rs->fields('topupdate'));
			$this->item->setDbValue($rs->fields('item'));
			$this->tid->setDbValue($rs->fields('tid'));
			$this->type->setDbValue($rs->fields('type'));
			$this->Val[1] = $this->oldlevel->CurrentValue;
			$this->Val[2] = $this->topuplevel->CurrentValue;
			$this->Val[3] = $this->topupdate->CurrentValue;
			$this->Val[4] = $this->item->CurrentValue;
			$this->Val[5] = $this->type->CurrentValue;
		} else {
			$this->oldlevel->setDbValue("");
			$this->topuplevel->setDbValue("");
			$this->topupdate->setDbValue("");
			$this->item->setDbValue("");
			$this->tid->setDbValue("");
			$this->type->setDbValue("");
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
			// Build distinct values for oldlevel

			if ($popupname == 'TopUpp_oldlevel') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->oldlevel, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->oldlevel->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->oldlevel->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->oldlevel->setDbValue($rswrk->fields[0]);
					if (is_null($this->oldlevel->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->oldlevel->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->oldlevel->ViewValue = $this->oldlevel->CurrentValue;
						ewr_SetupDistinctValues($this->oldlevel->ValueList, $this->oldlevel->CurrentValue, $this->oldlevel->ViewValue, FALSE, $this->oldlevel->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->oldlevel->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->oldlevel->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->oldlevel;
			}

			// Build distinct values for topuplevel
			if ($popupname == 'TopUpp_topuplevel') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->topuplevel, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->topuplevel->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->topuplevel->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->topuplevel->setDbValue($rswrk->fields[0]);
					if (is_null($this->topuplevel->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->topuplevel->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->topuplevel->ViewValue = $this->topuplevel->CurrentValue;
						ewr_SetupDistinctValues($this->topuplevel->ValueList, $this->topuplevel->CurrentValue, $this->topuplevel->ViewValue, FALSE, $this->topuplevel->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->topuplevel->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->topuplevel->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->topuplevel;
			}

			// Build distinct values for topupdate
			if ($popupname == 'TopUpp_topupdate') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->topupdate, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->topupdate->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->topupdate->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->topupdate->setDbValue($rswrk->fields[0]);
					if (is_null($this->topupdate->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->topupdate->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->topupdate->ViewValue = ewr_FormatDateTime($this->topupdate->CurrentValue, 1);
						ewr_SetupDistinctValues($this->topupdate->ValueList, $this->topupdate->CurrentValue, $this->topupdate->ViewValue, FALSE, $this->topupdate->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->topupdate->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->topupdate->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->topupdate;
			}

			// Build distinct values for item
			if ($popupname == 'TopUpp_item') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->item, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->item->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->item->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->item->setDbValue($rswrk->fields[0]);
					if (is_null($this->item->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->item->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->item->ViewValue = $this->item->CurrentValue;
						ewr_SetupDistinctValues($this->item->ValueList, $this->item->CurrentValue, $this->item->ViewValue, FALSE, $this->item->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->item->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->item->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->item;
			}

			// Build distinct values for type
			if ($popupname == 'TopUpp_type') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->type, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->type->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->type->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->type->setDbValue($rswrk->fields[0]);
					if (is_null($this->type->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->type->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						$this->type->ViewValue = $this->type->CurrentValue;
						ewr_SetupDistinctValues($this->type->ValueList, $this->type->CurrentValue, $this->type->ViewValue, FALSE, $this->type->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->type->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->type->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->type;
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
				$this->ClearSessionSelection('oldlevel');
				$this->ClearSessionSelection('topuplevel');
				$this->ClearSessionSelection('topupdate');
				$this->ClearSessionSelection('item');
				$this->ClearSessionSelection('type');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get oldlevel selected values

		if (is_array(@$_SESSION["sel_TopUpp_oldlevel"])) {
			$this->LoadSelectionFromSession('oldlevel');
		} elseif (@$_SESSION["sel_TopUpp_oldlevel"] == EWR_INIT_VALUE) { // Select all
			$this->oldlevel->SelectionList = "";
		}

		// Get topuplevel selected values
		if (is_array(@$_SESSION["sel_TopUpp_topuplevel"])) {
			$this->LoadSelectionFromSession('topuplevel');
		} elseif (@$_SESSION["sel_TopUpp_topuplevel"] == EWR_INIT_VALUE) { // Select all
			$this->topuplevel->SelectionList = "";
		}

		// Get topupdate selected values
		if (is_array(@$_SESSION["sel_TopUpp_topupdate"])) {
			$this->LoadSelectionFromSession('topupdate');
		} elseif (@$_SESSION["sel_TopUpp_topupdate"] == EWR_INIT_VALUE) { // Select all
			$this->topupdate->SelectionList = "";
		}

		// Get item selected values
		if (is_array(@$_SESSION["sel_TopUpp_item"])) {
			$this->LoadSelectionFromSession('item');
		} elseif (@$_SESSION["sel_TopUpp_item"] == EWR_INIT_VALUE) { // Select all
			$this->item->SelectionList = "";
		}

		// Get type selected values
		if (is_array(@$_SESSION["sel_TopUpp_type"])) {
			$this->LoadSelectionFromSession('type');
		} elseif (@$_SESSION["sel_TopUpp_type"] == EWR_INIT_VALUE) { // Select all
			$this->type->SelectionList = "";
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

			// oldlevel
			$this->oldlevel->HrefValue = "";

			// topuplevel
			$this->topuplevel->HrefValue = "";

			// topupdate
			$this->topupdate->HrefValue = "";

			// item
			$this->item->HrefValue = "";

			// type
			$this->type->HrefValue = "";
		} else {

			// oldlevel
			$this->oldlevel->ViewValue = $this->oldlevel->CurrentValue;
			$this->oldlevel->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// topuplevel
			$this->topuplevel->ViewValue = $this->topuplevel->CurrentValue;
			$this->topuplevel->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// topupdate
			$this->topupdate->ViewValue = $this->topupdate->CurrentValue;
			$this->topupdate->ViewValue = ewr_FormatDateTime($this->topupdate->ViewValue, 1);
			$this->topupdate->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// item
			$this->item->ViewValue = $this->item->CurrentValue;
			$this->item->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// type
			$this->type->ViewValue = $this->type->CurrentValue;
			$this->type->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// oldlevel
			$this->oldlevel->HrefValue = "";

			// topuplevel
			$this->topuplevel->HrefValue = "";

			// topupdate
			$this->topupdate->HrefValue = "";

			// item
			$this->item->HrefValue = "";

			// type
			$this->type->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// oldlevel
			$CurrentValue = $this->oldlevel->CurrentValue;
			$ViewValue = &$this->oldlevel->ViewValue;
			$ViewAttrs = &$this->oldlevel->ViewAttrs;
			$CellAttrs = &$this->oldlevel->CellAttrs;
			$HrefValue = &$this->oldlevel->HrefValue;
			$LinkAttrs = &$this->oldlevel->LinkAttrs;
			$this->Cell_Rendered($this->oldlevel, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// topuplevel
			$CurrentValue = $this->topuplevel->CurrentValue;
			$ViewValue = &$this->topuplevel->ViewValue;
			$ViewAttrs = &$this->topuplevel->ViewAttrs;
			$CellAttrs = &$this->topuplevel->CellAttrs;
			$HrefValue = &$this->topuplevel->HrefValue;
			$LinkAttrs = &$this->topuplevel->LinkAttrs;
			$this->Cell_Rendered($this->topuplevel, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// topupdate
			$CurrentValue = $this->topupdate->CurrentValue;
			$ViewValue = &$this->topupdate->ViewValue;
			$ViewAttrs = &$this->topupdate->ViewAttrs;
			$CellAttrs = &$this->topupdate->CellAttrs;
			$HrefValue = &$this->topupdate->HrefValue;
			$LinkAttrs = &$this->topupdate->LinkAttrs;
			$this->Cell_Rendered($this->topupdate, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// item
			$CurrentValue = $this->item->CurrentValue;
			$ViewValue = &$this->item->ViewValue;
			$ViewAttrs = &$this->item->ViewAttrs;
			$CellAttrs = &$this->item->CellAttrs;
			$HrefValue = &$this->item->HrefValue;
			$LinkAttrs = &$this->item->LinkAttrs;
			$this->Cell_Rendered($this->item, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// type
			$CurrentValue = $this->type->CurrentValue;
			$ViewValue = &$this->type->ViewValue;
			$ViewAttrs = &$this->type->ViewAttrs;
			$CellAttrs = &$this->type->CellAttrs;
			$HrefValue = &$this->type->HrefValue;
			$LinkAttrs = &$this->type->LinkAttrs;
			$this->Cell_Rendered($this->type, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
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
		if ($this->oldlevel->Visible) $this->DtlFldCount += 1;
		if ($this->topuplevel->Visible) $this->DtlFldCount += 1;
		if ($this->topupdate->Visible) $this->DtlFldCount += 1;
		if ($this->item->Visible) $this->DtlFldCount += 1;
		if ($this->type->Visible) $this->DtlFldCount += 1;
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

			// Clear extended filter for field oldlevel
			if ($this->ClearExtFilter == 'TopUpp_oldlevel')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'oldlevel');

			// Clear extended filter for field topuplevel
			if ($this->ClearExtFilter == 'TopUpp_topuplevel')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'topuplevel');

			// Clear extended filter for field topupdate
			if ($this->ClearExtFilter == 'TopUpp_topupdate')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'topupdate');

			// Clear extended filter for field item
			if ($this->ClearExtFilter == 'TopUpp_item')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'item');

			// Clear extended filter for field type
			if ($this->ClearExtFilter == 'TopUpp_type')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'type');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->oldlevel->SearchValue, $this->oldlevel->SearchOperator, $this->oldlevel->SearchCondition, $this->oldlevel->SearchValue2, $this->oldlevel->SearchOperator2, 'oldlevel'); // Field oldlevel
			$this->SetSessionFilterValues($this->topuplevel->SearchValue, $this->topuplevel->SearchOperator, $this->topuplevel->SearchCondition, $this->topuplevel->SearchValue2, $this->topuplevel->SearchOperator2, 'topuplevel'); // Field topuplevel
			$this->SetSessionFilterValues($this->topupdate->SearchValue, $this->topupdate->SearchOperator, $this->topupdate->SearchCondition, $this->topupdate->SearchValue2, $this->topupdate->SearchOperator2, 'topupdate'); // Field topupdate
			$this->SetSessionFilterValues($this->item->SearchValue, $this->item->SearchOperator, $this->item->SearchCondition, $this->item->SearchValue2, $this->item->SearchOperator2, 'item'); // Field item
			$this->SetSessionFilterValues($this->type->SearchValue, $this->type->SearchOperator, $this->type->SearchCondition, $this->type->SearchValue2, $this->type->SearchOperator2, 'type'); // Field type

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field oldlevel
			if ($this->GetFilterValues($this->oldlevel)) {
				$bSetupFilter = TRUE;
			}

			// Field topuplevel
			if ($this->GetFilterValues($this->topuplevel)) {
				$bSetupFilter = TRUE;
			}

			// Field topupdate
			if ($this->GetFilterValues($this->topupdate)) {
				$bSetupFilter = TRUE;
			}

			// Field item
			if ($this->GetFilterValues($this->item)) {
				$bSetupFilter = TRUE;
			}

			// Field type
			if ($this->GetFilterValues($this->type)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->oldlevel); // Field oldlevel
			$this->GetSessionFilterValues($this->topuplevel); // Field topuplevel
			$this->GetSessionFilterValues($this->topupdate); // Field topupdate
			$this->GetSessionFilterValues($this->item); // Field item
			$this->GetSessionFilterValues($this->type); // Field type
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->oldlevel, $sFilter, FALSE, TRUE); // Field oldlevel
		$this->BuildExtendedFilter($this->topuplevel, $sFilter, FALSE, TRUE); // Field topuplevel
		$this->BuildExtendedFilter($this->topupdate, $sFilter, FALSE, TRUE); // Field topupdate
		$this->BuildExtendedFilter($this->item, $sFilter, FALSE, TRUE); // Field item
		$this->BuildExtendedFilter($this->type, $sFilter, FALSE, TRUE); // Field type

		// Save parms to session
		$this->SetSessionFilterValues($this->oldlevel->SearchValue, $this->oldlevel->SearchOperator, $this->oldlevel->SearchCondition, $this->oldlevel->SearchValue2, $this->oldlevel->SearchOperator2, 'oldlevel'); // Field oldlevel
		$this->SetSessionFilterValues($this->topuplevel->SearchValue, $this->topuplevel->SearchOperator, $this->topuplevel->SearchCondition, $this->topuplevel->SearchValue2, $this->topuplevel->SearchOperator2, 'topuplevel'); // Field topuplevel
		$this->SetSessionFilterValues($this->topupdate->SearchValue, $this->topupdate->SearchOperator, $this->topupdate->SearchCondition, $this->topupdate->SearchValue2, $this->topupdate->SearchOperator2, 'topupdate'); // Field topupdate
		$this->SetSessionFilterValues($this->item->SearchValue, $this->item->SearchOperator, $this->item->SearchCondition, $this->item->SearchValue2, $this->item->SearchOperator2, 'item'); // Field item
		$this->SetSessionFilterValues($this->type->SearchValue, $this->type->SearchOperator, $this->type->SearchCondition, $this->type->SearchValue2, $this->type->SearchOperator2, 'type'); // Field type

		// Setup filter
		if ($bSetupFilter) {

			// Field oldlevel
			$sWrk = "";
			$this->BuildExtendedFilter($this->oldlevel, $sWrk);
			ewr_LoadSelectionFromFilter($this->oldlevel, $sWrk, $this->oldlevel->SelectionList);
			$_SESSION['sel_TopUpp_oldlevel'] = ($this->oldlevel->SelectionList == "") ? EWR_INIT_VALUE : $this->oldlevel->SelectionList;

			// Field topuplevel
			$sWrk = "";
			$this->BuildExtendedFilter($this->topuplevel, $sWrk);
			ewr_LoadSelectionFromFilter($this->topuplevel, $sWrk, $this->topuplevel->SelectionList);
			$_SESSION['sel_TopUpp_topuplevel'] = ($this->topuplevel->SelectionList == "") ? EWR_INIT_VALUE : $this->topuplevel->SelectionList;

			// Field topupdate
			$sWrk = "";
			$this->BuildExtendedFilter($this->topupdate, $sWrk);
			ewr_LoadSelectionFromFilter($this->topupdate, $sWrk, $this->topupdate->SelectionList);
			$_SESSION['sel_TopUpp_topupdate'] = ($this->topupdate->SelectionList == "") ? EWR_INIT_VALUE : $this->topupdate->SelectionList;

			// Field item
			$sWrk = "";
			$this->BuildExtendedFilter($this->item, $sWrk);
			ewr_LoadSelectionFromFilter($this->item, $sWrk, $this->item->SelectionList);
			$_SESSION['sel_TopUpp_item'] = ($this->item->SelectionList == "") ? EWR_INIT_VALUE : $this->item->SelectionList;

			// Field type
			$sWrk = "";
			$this->BuildExtendedFilter($this->type, $sWrk);
			ewr_LoadSelectionFromFilter($this->type, $sWrk, $this->type->SelectionList);
			$_SESSION['sel_TopUpp_type'] = ($this->type->SelectionList == "") ? EWR_INIT_VALUE : $this->type->SelectionList;
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
		$this->GetSessionValue($fld->DropDownValue, 'sv_TopUpp_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_TopUpp_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_TopUpp_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_TopUpp_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_TopUpp_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_TopUpp_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_TopUpp_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_TopUpp_' . $parm] = $sv;
		$_SESSION['so_TopUpp_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_TopUpp_' . $parm] = $sv1;
		$_SESSION['so_TopUpp_' . $parm] = $so1;
		$_SESSION['sc_TopUpp_' . $parm] = $sc;
		$_SESSION['sv2_TopUpp_' . $parm] = $sv2;
		$_SESSION['so2_TopUpp_' . $parm] = $so2;
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
		if (!ewr_CheckNumber($this->oldlevel->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->oldlevel->FldErrMsg();
		}
		if (!ewr_CheckNumber($this->topuplevel->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->topuplevel->FldErrMsg();
		}
		if (!ewr_CheckDate($this->topupdate->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->topupdate->FldErrMsg();
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
		$_SESSION["sel_TopUpp_$parm"] = "";
		$_SESSION["rf_TopUpp_$parm"] = "";
		$_SESSION["rt_TopUpp_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_TopUpp_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_TopUpp_$parm"];
		$fld->RangeTo = @$_SESSION["rt_TopUpp_$parm"];
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

		// Field oldlevel
		$this->SetDefaultExtFilter($this->oldlevel, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->oldlevel);
		$sWrk = "";
		$this->BuildExtendedFilter($this->oldlevel, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->oldlevel, $sWrk, $this->oldlevel->DefaultSelectionList);
		if (!$this->SearchCommand) $this->oldlevel->SelectionList = $this->oldlevel->DefaultSelectionList;

		// Field topuplevel
		$this->SetDefaultExtFilter($this->topuplevel, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->topuplevel);
		$sWrk = "";
		$this->BuildExtendedFilter($this->topuplevel, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->topuplevel, $sWrk, $this->topuplevel->DefaultSelectionList);
		if (!$this->SearchCommand) $this->topuplevel->SelectionList = $this->topuplevel->DefaultSelectionList;

		// Field topupdate
		$this->SetDefaultExtFilter($this->topupdate, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->topupdate);
		$sWrk = "";
		$this->BuildExtendedFilter($this->topupdate, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->topupdate, $sWrk, $this->topupdate->DefaultSelectionList);
		if (!$this->SearchCommand) $this->topupdate->SelectionList = $this->topupdate->DefaultSelectionList;

		// Field item
		$this->SetDefaultExtFilter($this->item, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->item);
		$sWrk = "";
		$this->BuildExtendedFilter($this->item, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->item, $sWrk, $this->item->DefaultSelectionList);
		if (!$this->SearchCommand) $this->item->SelectionList = $this->item->DefaultSelectionList;

		// Field type
		$this->SetDefaultExtFilter($this->type, "USER SELECT", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->type);
		$sWrk = "";
		$this->BuildExtendedFilter($this->type, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->type, $sWrk, $this->type->DefaultSelectionList);
		if (!$this->SearchCommand) $this->type->SelectionList = $this->type->DefaultSelectionList;

		/**
		* Set up default values for popup filters
		*/

		// Field oldlevel
		// $this->oldlevel->DefaultSelectionList = array("val1", "val2");
		// Field topuplevel
		// $this->topuplevel->DefaultSelectionList = array("val1", "val2");
		// Field topupdate
		// $this->topupdate->DefaultSelectionList = array("val1", "val2");
		// Field item
		// $this->item->DefaultSelectionList = array("val1", "val2");
		// Field type
		// $this->type->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check oldlevel text filter
		if ($this->TextFilterApplied($this->oldlevel))
			return TRUE;

		// Check oldlevel popup filter
		if (!ewr_MatchedArray($this->oldlevel->DefaultSelectionList, $this->oldlevel->SelectionList))
			return TRUE;

		// Check topuplevel text filter
		if ($this->TextFilterApplied($this->topuplevel))
			return TRUE;

		// Check topuplevel popup filter
		if (!ewr_MatchedArray($this->topuplevel->DefaultSelectionList, $this->topuplevel->SelectionList))
			return TRUE;

		// Check topupdate text filter
		if ($this->TextFilterApplied($this->topupdate))
			return TRUE;

		// Check topupdate popup filter
		if (!ewr_MatchedArray($this->topupdate->DefaultSelectionList, $this->topupdate->SelectionList))
			return TRUE;

		// Check item text filter
		if ($this->TextFilterApplied($this->item))
			return TRUE;

		// Check item popup filter
		if (!ewr_MatchedArray($this->item->DefaultSelectionList, $this->item->SelectionList))
			return TRUE;

		// Check type text filter
		if ($this->TextFilterApplied($this->type))
			return TRUE;

		// Check type popup filter
		if (!ewr_MatchedArray($this->type->DefaultSelectionList, $this->type->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field oldlevel
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->oldlevel, $sExtWrk);
		if (is_array($this->oldlevel->SelectionList))
			$sWrk = ewr_JoinArray($this->oldlevel->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->oldlevel->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field topuplevel
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->topuplevel, $sExtWrk);
		if (is_array($this->topuplevel->SelectionList))
			$sWrk = ewr_JoinArray($this->topuplevel->SelectionList, ", ", EWR_DATATYPE_NUMBER, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->topuplevel->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field topupdate
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->topupdate, $sExtWrk);
		if (is_array($this->topupdate->SelectionList))
			$sWrk = ewr_JoinArray($this->topupdate->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->topupdate->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field item
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->item, $sExtWrk);
		if (is_array($this->item->SelectionList))
			$sWrk = ewr_JoinArray($this->item->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->item->FldCaption() . "</span>" . $sFilter . "</div>";

		// Field type
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->type, $sExtWrk);
		if (is_array($this->type->SelectionList))
			$sWrk = ewr_JoinArray($this->type->SelectionList, ", ", EWR_DATATYPE_STRING, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->type->FldCaption() . "</span>" . $sFilter . "</div>";
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

		// Field oldlevel
		$sWrk = "";
		if ($this->oldlevel->SearchValue <> "" || $this->oldlevel->SearchValue2 <> "") {
			$sWrk = "\"sv_oldlevel\":\"" . ewr_JsEncode2($this->oldlevel->SearchValue) . "\"," .
				"\"so_oldlevel\":\"" . ewr_JsEncode2($this->oldlevel->SearchOperator) . "\"," .
				"\"sc_oldlevel\":\"" . ewr_JsEncode2($this->oldlevel->SearchCondition) . "\"," .
				"\"sv2_oldlevel\":\"" . ewr_JsEncode2($this->oldlevel->SearchValue2) . "\"," .
				"\"so2_oldlevel\":\"" . ewr_JsEncode2($this->oldlevel->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->oldlevel->SelectionList <> EWR_INIT_VALUE) ? $this->oldlevel->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_oldlevel\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field topuplevel
		$sWrk = "";
		if ($this->topuplevel->SearchValue <> "" || $this->topuplevel->SearchValue2 <> "") {
			$sWrk = "\"sv_topuplevel\":\"" . ewr_JsEncode2($this->topuplevel->SearchValue) . "\"," .
				"\"so_topuplevel\":\"" . ewr_JsEncode2($this->topuplevel->SearchOperator) . "\"," .
				"\"sc_topuplevel\":\"" . ewr_JsEncode2($this->topuplevel->SearchCondition) . "\"," .
				"\"sv2_topuplevel\":\"" . ewr_JsEncode2($this->topuplevel->SearchValue2) . "\"," .
				"\"so2_topuplevel\":\"" . ewr_JsEncode2($this->topuplevel->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->topuplevel->SelectionList <> EWR_INIT_VALUE) ? $this->topuplevel->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_topuplevel\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field topupdate
		$sWrk = "";
		if ($this->topupdate->SearchValue <> "" || $this->topupdate->SearchValue2 <> "") {
			$sWrk = "\"sv_topupdate\":\"" . ewr_JsEncode2($this->topupdate->SearchValue) . "\"," .
				"\"so_topupdate\":\"" . ewr_JsEncode2($this->topupdate->SearchOperator) . "\"," .
				"\"sc_topupdate\":\"" . ewr_JsEncode2($this->topupdate->SearchCondition) . "\"," .
				"\"sv2_topupdate\":\"" . ewr_JsEncode2($this->topupdate->SearchValue2) . "\"," .
				"\"so2_topupdate\":\"" . ewr_JsEncode2($this->topupdate->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->topupdate->SelectionList <> EWR_INIT_VALUE) ? $this->topupdate->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_topupdate\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field item
		$sWrk = "";
		if ($this->item->SearchValue <> "" || $this->item->SearchValue2 <> "") {
			$sWrk = "\"sv_item\":\"" . ewr_JsEncode2($this->item->SearchValue) . "\"," .
				"\"so_item\":\"" . ewr_JsEncode2($this->item->SearchOperator) . "\"," .
				"\"sc_item\":\"" . ewr_JsEncode2($this->item->SearchCondition) . "\"," .
				"\"sv2_item\":\"" . ewr_JsEncode2($this->item->SearchValue2) . "\"," .
				"\"so2_item\":\"" . ewr_JsEncode2($this->item->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->item->SelectionList <> EWR_INIT_VALUE) ? $this->item->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_item\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Field type
		$sWrk = "";
		if ($this->type->SearchValue <> "" || $this->type->SearchValue2 <> "") {
			$sWrk = "\"sv_type\":\"" . ewr_JsEncode2($this->type->SearchValue) . "\"," .
				"\"so_type\":\"" . ewr_JsEncode2($this->type->SearchOperator) . "\"," .
				"\"sc_type\":\"" . ewr_JsEncode2($this->type->SearchCondition) . "\"," .
				"\"sv2_type\":\"" . ewr_JsEncode2($this->type->SearchValue2) . "\"," .
				"\"so2_type\":\"" . ewr_JsEncode2($this->type->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->type->SelectionList <> EWR_INIT_VALUE) ? $this->type->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_type\":\"" . ewr_JsEncode2($sWrk) . "\"";
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

		// Field oldlevel
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_oldlevel", $filter) || array_key_exists("so_oldlevel", $filter) ||
			array_key_exists("sc_oldlevel", $filter) ||
			array_key_exists("sv2_oldlevel", $filter) || array_key_exists("so2_oldlevel", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_oldlevel"], @$filter["so_oldlevel"], @$filter["sc_oldlevel"], @$filter["sv2_oldlevel"], @$filter["so2_oldlevel"], "oldlevel");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_oldlevel", $filter)) {
			$sWrk = $filter["sel_oldlevel"];
			$sWrk = explode("||", $sWrk);
			$this->oldlevel->SelectionList = $sWrk;
			$_SESSION["sel_TopUpp_oldlevel"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "oldlevel"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "oldlevel");
			$this->oldlevel->SelectionList = "";
			$_SESSION["sel_TopUpp_oldlevel"] = "";
		}

		// Field topuplevel
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_topuplevel", $filter) || array_key_exists("so_topuplevel", $filter) ||
			array_key_exists("sc_topuplevel", $filter) ||
			array_key_exists("sv2_topuplevel", $filter) || array_key_exists("so2_topuplevel", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_topuplevel"], @$filter["so_topuplevel"], @$filter["sc_topuplevel"], @$filter["sv2_topuplevel"], @$filter["so2_topuplevel"], "topuplevel");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_topuplevel", $filter)) {
			$sWrk = $filter["sel_topuplevel"];
			$sWrk = explode("||", $sWrk);
			$this->topuplevel->SelectionList = $sWrk;
			$_SESSION["sel_TopUpp_topuplevel"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "topuplevel"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "topuplevel");
			$this->topuplevel->SelectionList = "";
			$_SESSION["sel_TopUpp_topuplevel"] = "";
		}

		// Field topupdate
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_topupdate", $filter) || array_key_exists("so_topupdate", $filter) ||
			array_key_exists("sc_topupdate", $filter) ||
			array_key_exists("sv2_topupdate", $filter) || array_key_exists("so2_topupdate", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_topupdate"], @$filter["so_topupdate"], @$filter["sc_topupdate"], @$filter["sv2_topupdate"], @$filter["so2_topupdate"], "topupdate");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_topupdate", $filter)) {
			$sWrk = $filter["sel_topupdate"];
			$sWrk = explode("||", $sWrk);
			$this->topupdate->SelectionList = $sWrk;
			$_SESSION["sel_TopUpp_topupdate"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "topupdate"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "topupdate");
			$this->topupdate->SelectionList = "";
			$_SESSION["sel_TopUpp_topupdate"] = "";
		}

		// Field item
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_item", $filter) || array_key_exists("so_item", $filter) ||
			array_key_exists("sc_item", $filter) ||
			array_key_exists("sv2_item", $filter) || array_key_exists("so2_item", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_item"], @$filter["so_item"], @$filter["sc_item"], @$filter["sv2_item"], @$filter["so2_item"], "item");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_item", $filter)) {
			$sWrk = $filter["sel_item"];
			$sWrk = explode("||", $sWrk);
			$this->item->SelectionList = $sWrk;
			$_SESSION["sel_TopUpp_item"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "item"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "item");
			$this->item->SelectionList = "";
			$_SESSION["sel_TopUpp_item"] = "";
		}

		// Field type
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_type", $filter) || array_key_exists("so_type", $filter) ||
			array_key_exists("sc_type", $filter) ||
			array_key_exists("sv2_type", $filter) || array_key_exists("so2_type", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_type"], @$filter["so_type"], @$filter["sc_type"], @$filter["sv2_type"], @$filter["so2_type"], "type");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_type", $filter)) {
			$sWrk = $filter["sel_type"];
			$sWrk = explode("||", $sWrk);
			$this->type->SelectionList = $sWrk;
			$_SESSION["sel_TopUpp_type"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "type"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "type");
			$this->type->SelectionList = "";
			$_SESSION["sel_TopUpp_type"] = "";
		}
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->oldlevel)) {
			if (is_array($this->oldlevel->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->oldlevel, "`oldlevel`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->oldlevel, $sFilter, "popup");
				$this->oldlevel->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->topuplevel)) {
			if (is_array($this->topuplevel->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->topuplevel, "`topuplevel`", EWR_DATATYPE_NUMBER, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->topuplevel, $sFilter, "popup");
				$this->topuplevel->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->topupdate)) {
			if (is_array($this->topupdate->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->topupdate, "`topupdate`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->topupdate, $sFilter, "popup");
				$this->topupdate->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->item)) {
			if (is_array($this->item->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->item, "`item`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->item, $sFilter, "popup");
				$this->item->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		if (!$this->ExtendedFilterExist($this->type)) {
			if (is_array($this->type->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->type, "`type`", EWR_DATATYPE_STRING, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->type, $sFilter, "popup");
				$this->type->CurrentFilter = $sFilter;
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
				$this->oldlevel->setSort("");
				$this->topuplevel->setSort("");
				$this->topupdate->setSort("");
				$this->item->setSort("");
				$this->type->setSort("");
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
if (!isset($TopUpp_summary)) $TopUpp_summary = new crTopUpp_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$TopUpp_summary;

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
var TopUpp_summary = new ewr_Page("TopUpp_summary");

// Page properties
TopUpp_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = TopUpp_summary.PageID;

// Extend page with Chart_Rendering function
TopUpp_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
TopUpp_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fTopUppsummary = new ewr_Form("fTopUppsummary");

// Validate method
fTopUppsummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_oldlevel;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->oldlevel->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_topuplevel;
	if (elm && !ewr_CheckNumber(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->topuplevel->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv_topupdate;
	if (elm && !ewr_CheckDate(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->topupdate->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fTopUppsummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fTopUppsummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fTopUppsummary.ValidateRequired = false; // No JavaScript validation
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
<form name="fTopUppsummary" id="fTopUppsummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fTopUppsummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_oldlevel" class="ewCell form-group">
	<label for="sv_oldlevel" class="ewSearchCaption ewLabel"><?php echo $Page->oldlevel->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_oldlevel" id="so_oldlevel" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->oldlevel->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->oldlevel->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->oldlevel->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->oldlevel->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->oldlevel->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->oldlevel->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->oldlevel->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->oldlevel->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->oldlevel->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->oldlevel->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_oldlevel" id="sv_oldlevel" name="sv_oldlevel" size="30" placeholder="<?php echo $Page->oldlevel->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->oldlevel->SearchValue) ?>"<?php echo $Page->oldlevel->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_oldlevel" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_oldlevel" style="display: none">
<?php ewr_PrependClass($Page->oldlevel->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_oldlevel" id="sv2_oldlevel" name="sv2_oldlevel" size="30" placeholder="<?php echo $Page->oldlevel->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->oldlevel->SearchValue2) ?>"<?php echo $Page->oldlevel->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_2" class="ewRow">
<div id="c_topuplevel" class="ewCell form-group">
	<label for="sv_topuplevel" class="ewSearchCaption ewLabel"><?php echo $Page->topuplevel->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_topuplevel" id="so_topuplevel" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->topuplevel->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->topuplevel->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->topuplevel->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->topuplevel->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->topuplevel->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->topuplevel->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->topuplevel->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->topuplevel->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->topuplevel->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->topuplevel->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_topuplevel" id="sv_topuplevel" name="sv_topuplevel" size="30" placeholder="<?php echo $Page->topuplevel->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->topuplevel->SearchValue) ?>"<?php echo $Page->topuplevel->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_topuplevel" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_topuplevel" style="display: none">
<?php ewr_PrependClass($Page->topuplevel->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_topuplevel" id="sv2_topuplevel" name="sv2_topuplevel" size="30" placeholder="<?php echo $Page->topuplevel->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->topuplevel->SearchValue2) ?>"<?php echo $Page->topuplevel->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_3" class="ewRow">
<div id="c_topupdate" class="ewCell form-group">
	<label for="sv_topupdate" class="ewSearchCaption ewLabel"><?php echo $Page->topupdate->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_topupdate" id="so_topupdate" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->topupdate->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->topupdate->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->topupdate->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->topupdate->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->topupdate->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->topupdate->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="IS NULL"<?php if ($Page->topupdate->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->topupdate->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->topupdate->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->topupdate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_topupdate" id="sv_topupdate" name="sv_topupdate" placeholder="<?php echo $Page->topupdate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->topupdate->SearchValue) ?>"<?php echo $Page->topupdate->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_topupdate" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_topupdate" style="display: none">
<?php ewr_PrependClass($Page->topupdate->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_topupdate" id="sv2_topupdate" name="sv2_topupdate" placeholder="<?php echo $Page->topupdate->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->topupdate->SearchValue2) ?>"<?php echo $Page->topupdate->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_4" class="ewRow">
<div id="c_item" class="ewCell form-group">
	<label for="sv_item" class="ewSearchCaption ewLabel"><?php echo $Page->item->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_item" id="so_item" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->item->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->item->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->item->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->item->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->item->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->item->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->item->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->item->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->item->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->item->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->item->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->item->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->item->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->item->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_item" id="sv_item" name="sv_item" size="30" maxlength="255" placeholder="<?php echo $Page->item->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->item->SearchValue) ?>"<?php echo $Page->item->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_item" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_item" style="display: none">
<?php ewr_PrependClass($Page->item->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_item" id="sv2_item" name="sv2_item" size="30" maxlength="255" placeholder="<?php echo $Page->item->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->item->SearchValue2) ?>"<?php echo $Page->item->EditAttributes() ?>>
</span>
</div>
</div>
<div id="r_5" class="ewRow">
<div id="c_type" class="ewCell form-group">
	<label for="sv_type" class="ewSearchCaption ewLabel"><?php echo $Page->type->FldCaption() ?></label>
	<span class="ewSearchOperator"><select name="so_type" id="so_type" class="form-control" onchange="ewrForms(this).SrchOprChanged(this);"><option value="="<?php if ($Page->type->SearchOperator == "=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("EQUAL"); ?></option><option value="<>"<?php if ($Page->type->SearchOperator == "<>") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<>"); ?></option><option value="<"<?php if ($Page->type->SearchOperator == "<") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<"); ?></option><option value="<="<?php if ($Page->type->SearchOperator == "<=") echo " selected" ?>><?php echo $ReportLanguage->Phrase("<="); ?></option><option value=">"<?php if ($Page->type->SearchOperator == ">") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">"); ?></option><option value=">="<?php if ($Page->type->SearchOperator == ">=") echo " selected" ?>><?php echo $ReportLanguage->Phrase(">="); ?></option><option value="LIKE"<?php if ($Page->type->SearchOperator == "LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("LIKE"); ?></option><option value="NOT LIKE"<?php if ($Page->type->SearchOperator == "NOT LIKE") echo " selected" ?>><?php echo $ReportLanguage->Phrase("NOT LIKE"); ?></option><option value="STARTS WITH"<?php if ($Page->type->SearchOperator == "STARTS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("STARTS WITH"); ?></option><option value="ENDS WITH"<?php if ($Page->type->SearchOperator == "ENDS WITH") echo " selected" ?>><?php echo $ReportLanguage->Phrase("ENDS WITH"); ?></option><option value="IS NULL"<?php if ($Page->type->SearchOperator == "IS NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NULL"); ?></option><option value="IS NOT NULL"<?php if ($Page->type->SearchOperator == "IS NOT NULL") echo " selected" ?>><?php echo $ReportLanguage->Phrase("IS NOT NULL"); ?></option><option value="BETWEEN"<?php if ($Page->type->SearchOperator == "BETWEEN") echo " selected" ?>><?php echo $ReportLanguage->Phrase("BETWEEN"); ?></option></select></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->type->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_type" id="sv_type" name="sv_type" size="30" maxlength="200" placeholder="<?php echo $Page->type->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->type->SearchValue) ?>"<?php echo $Page->type->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_type" style="display: none"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_type" style="display: none">
<?php ewr_PrependClass($Page->type->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="TopUpp" data-field="x_type" id="sv2_type" name="sv2_type" size="30" maxlength="200" placeholder="<?php echo $Page->type->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->type->SearchValue2) ?>"<?php echo $Page->type->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fTopUppsummary.Init();
fTopUppsummary.FilterList = <?php echo $Page->GetFilterList() ?>;
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
<?php include "TopUppsmrypager.php" ?>
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
<?php if ($Page->oldlevel->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="oldlevel"><div class="TopUpp_oldlevel"><span class="ewTableHeaderCaption"><?php echo $Page->oldlevel->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="oldlevel">
<?php if ($Page->SortUrl($Page->oldlevel) == "") { ?>
		<div class="ewTableHeaderBtn TopUpp_oldlevel">
			<span class="ewTableHeaderCaption"><?php echo $Page->oldlevel->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_oldlevel', false, '<?php echo $Page->oldlevel->RangeFrom; ?>', '<?php echo $Page->oldlevel->RangeTo; ?>');" id="x_oldlevel<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer TopUpp_oldlevel" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->oldlevel) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->oldlevel->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->oldlevel->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->oldlevel->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_oldlevel', false, '<?php echo $Page->oldlevel->RangeFrom; ?>', '<?php echo $Page->oldlevel->RangeTo; ?>');" id="x_oldlevel<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->topuplevel->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="topuplevel"><div class="TopUpp_topuplevel"><span class="ewTableHeaderCaption"><?php echo $Page->topuplevel->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="topuplevel">
<?php if ($Page->SortUrl($Page->topuplevel) == "") { ?>
		<div class="ewTableHeaderBtn TopUpp_topuplevel">
			<span class="ewTableHeaderCaption"><?php echo $Page->topuplevel->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_topuplevel', false, '<?php echo $Page->topuplevel->RangeFrom; ?>', '<?php echo $Page->topuplevel->RangeTo; ?>');" id="x_topuplevel<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer TopUpp_topuplevel" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->topuplevel) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->topuplevel->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->topuplevel->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->topuplevel->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_topuplevel', false, '<?php echo $Page->topuplevel->RangeFrom; ?>', '<?php echo $Page->topuplevel->RangeTo; ?>');" id="x_topuplevel<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->topupdate->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="topupdate"><div class="TopUpp_topupdate"><span class="ewTableHeaderCaption"><?php echo $Page->topupdate->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="topupdate">
<?php if ($Page->SortUrl($Page->topupdate) == "") { ?>
		<div class="ewTableHeaderBtn TopUpp_topupdate">
			<span class="ewTableHeaderCaption"><?php echo $Page->topupdate->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_topupdate', false, '<?php echo $Page->topupdate->RangeFrom; ?>', '<?php echo $Page->topupdate->RangeTo; ?>');" id="x_topupdate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer TopUpp_topupdate" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->topupdate) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->topupdate->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->topupdate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->topupdate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_topupdate', false, '<?php echo $Page->topupdate->RangeFrom; ?>', '<?php echo $Page->topupdate->RangeTo; ?>');" id="x_topupdate<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->item->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="item"><div class="TopUpp_item"><span class="ewTableHeaderCaption"><?php echo $Page->item->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="item">
<?php if ($Page->SortUrl($Page->item) == "") { ?>
		<div class="ewTableHeaderBtn TopUpp_item">
			<span class="ewTableHeaderCaption"><?php echo $Page->item->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_item', false, '<?php echo $Page->item->RangeFrom; ?>', '<?php echo $Page->item->RangeTo; ?>');" id="x_item<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer TopUpp_item" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->item) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->item->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->item->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->item->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_item', false, '<?php echo $Page->item->RangeFrom; ?>', '<?php echo $Page->item->RangeTo; ?>');" id="x_item<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->type->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="type"><div class="TopUpp_type"><span class="ewTableHeaderCaption"><?php echo $Page->type->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="type">
<?php if ($Page->SortUrl($Page->type) == "") { ?>
		<div class="ewTableHeaderBtn TopUpp_type">
			<span class="ewTableHeaderCaption"><?php echo $Page->type->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_type', false, '<?php echo $Page->type->RangeFrom; ?>', '<?php echo $Page->type->RangeTo; ?>');" id="x_type<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer TopUpp_type" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->type) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->type->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'TopUpp_type', false, '<?php echo $Page->type->RangeFrom; ?>', '<?php echo $Page->type->RangeTo; ?>');" id="x_type<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
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
<?php if ($Page->oldlevel->Visible) { ?>
		<td data-field="oldlevel"<?php echo $Page->oldlevel->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_TopUpp_oldlevel"<?php echo $Page->oldlevel->ViewAttributes() ?>><?php echo $Page->oldlevel->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->topuplevel->Visible) { ?>
		<td data-field="topuplevel"<?php echo $Page->topuplevel->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_TopUpp_topuplevel"<?php echo $Page->topuplevel->ViewAttributes() ?>><?php echo $Page->topuplevel->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->topupdate->Visible) { ?>
		<td data-field="topupdate"<?php echo $Page->topupdate->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_TopUpp_topupdate"<?php echo $Page->topupdate->ViewAttributes() ?>><?php echo $Page->topupdate->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->item->Visible) { ?>
		<td data-field="item"<?php echo $Page->item->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_TopUpp_item"<?php echo $Page->item->ViewAttributes() ?>><?php echo $Page->item->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->type->Visible) { ?>
		<td data-field="type"<?php echo $Page->type->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_TopUpp_type"<?php echo $Page->type->ViewAttributes() ?>><?php echo $Page->type->ListViewValue() ?></span></td>
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
<?php include "TopUppsmrypager.php" ?>
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
<?php include "TopUppsmrypager.php" ?>
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
