<?php
   session_start();
   include("config.php");

   if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
   		session_destroy();
	    header("location: index.php");
	    exit;
	}

   //global $search_result;
   if (isset($_POST['create_pdf'])) {
		require_once("generatePdf/tcpdf.php");
		$pdf_obj = new TCPDF('P', PDF_UNIT,PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf_obj->SetCreator(PDF_CREATOR);
		$pdf_obj->SetTitle("Export search result to PDF");
		$pdf_obj->SetHeaderData('','',PDF_HEADER_TITLE,PDF_HEADER_STRING);
		$pdf_obj->SetHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
		$pdf_obj->SetFooterFont(Array(PDF_FONT_NAME_DATA,'',PDF_FONT_SIZE_DATA));
		$pdf_obj->SetDefaultMonospacedFont('helvetica');
		$pdf_obj->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf_obj->SetMargins(PDF_MARGIN_LEFT,'5',PDF_MARGIN_RIGHT);
		$pdf_obj->setPrintHeader(false);
		$pdf_obj->setPrintFooter(false);
		$pdf_obj->SetAutoPageBreak(TRUE, 10);
		$pdf_obj->SetFont('helvetica','',12);

		$content = '';
		$content .= '<h1>SEARCH RESULTS</h1>';
		$json = json_decode($_POST['search_result']);
		foreach ($json->items as $result) {		
			$content .= '<h3 style="color: #1A0DAA; font-size: x-large">'.$result->htmlTitle.'</h3>
			<div>'.$result->displayLink.'</div><br><div>'.$result->snippet.'</div>';
		}

		$pdf_obj->AddPage();
		$pdf_obj->writeHTML($content, true, false, true, false, '');		
		$pdf_obj->Output("sample.pdf","I");
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" href="CSS/bootstrap.css">
	<style type = "text/css">
        .center{
        	text-align: center;
        }
        .wrapper-search{ width: 500px; margin :20px; padding: 20px; }
      </style>
</head>
<body style="margin-left:20px;">
<div style="float: right; margin-right: 10px">
	<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
<div class="wrapper-search">
	<h2 class="center">Search </h2>
	<form method="get" action="">
	<div class="form-group">
		<input name="results" class="form-control" />
	</div>
	<div class="form-group center">
		<input type="submit" class="btn btn-primary"/>
	</div>
	</form>
</div>

<?php
$search = $_GET['results'];
if(isset($_GET['results']) && $_GET['results'] != ""){
		
	echo "<br />Your Search Result Array:<br /><br />";
	echo "<form method='post' action=''><input type='submit' name='create_pdf' value='Save Result' class='btn btn-primary'/>";

	$query = str_replace(" ","+",$_GET['results']);
	$url = "https://www.googleapis.com/customsearch/v1?key=".SEARCH_API_KEY."&cx=".SEARCH_ENGINE_ID."&q=".$query;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com');
	$body = curl_exec($ch);
	curl_close($ch);
	$search_result = json_decode($body);
	/* Sample response for testing purpose */
	if(isset($search_result->error)) {
		$search_result = SEARCH_RESULT;
	} else {
		$search_result = $search_result;
	}
	$json = json_decode($search_result);
	foreach ($json->items as $result) {		
		?>
		<h3 style="color: #1A0DAA; font-size: x-large"><?=$result->htmlTitle?></h3>
		<div><?=$result->displayLink?></div><br>
		<div><?=$result->snippet?></div>
		<?php 
	}

	echo "<input type='hidden' name='search_result' value='".$search_result."'/>";
	echo "</form>";
}
?>