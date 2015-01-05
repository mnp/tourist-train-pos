<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?= @$page_title ?></title>
<link href="../styles/report.css" rel="stylesheet" type="text/css" />
<body>
<?= MNP::close_button('Close') ?>
&nbsp;
<?= MNP::print_button('Print');
     if(isset($page_title)) { echo "<center><h2>$page_title</h2></center>"; }
?>    

<script language="JavaScript" type="text/javascript">
<!--

function submitAction(action, id, page)
{
  if (page) {
    document.ReportForm.action = page;
  }
  document.ReportForm.actionName.value = action;
  document.ReportForm.actionId.value = id;
  document.ReportForm.submit();
}
//-->
</script>

<form method="POST" name="ReportForm" enctype="multipart/form-data" action="#">
<input type="hidden" name="actionId" value="" />
<input type="hidden" name="actionName" value="" />
