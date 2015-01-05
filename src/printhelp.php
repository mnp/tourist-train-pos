<?  
require_once '../lib/base.php';
$page_title = 'Collected Help Topics';
include '../templates/report-header.inc';

$files = array();

$dirp = dir(ROOTPATH . '/helpdocs');
while (false !== ($f = $dirp->read())) {
  if (!preg_match('/^\./', $f) && !preg_match('/~$/', $f)) {
    $files[] = $f;
  }
}
$dirp->close();

sort($files);

foreach ($files as $f) {
  $topic = basename($f,'.html');
  $topic = preg_replace('/_/', ' ', $topic);
  echo "<h3>$topic</h3>";
  readfile(ROOTPATH . '/helpdocs/' . $f);
}

include '../templates/report-footer.inc';

?>

