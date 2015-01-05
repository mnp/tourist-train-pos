
<script language="JavaScript" type="text/javascript">
<!--

function confirmSomething(thing)
{
  var yes=confirm("Really delete customer?  NOTE: This will also delete all reservations for this customer.  On the other hand, deleting a reservation will not affect its customer.");
  return yes ? true : false; 
}

function resetForm()
{
  document.forms['<?= $formname ?>'].reset();
}

function submitAction(action, id, page)
{
  if (page) {
    document.<?= $formname ?>.action = page;
  }
  document.<?= $formname ?>.actionName.value = action;
  document.<?= $formname ?>.actionId.value = id;
  document.<?= $formname ?>.submit();
}

function chooseAction()
{
  document.<?= $formname ?>.submit();
}

function increment(field)
{
  if (document.<?= $formname ?>.elements[field].disabled) {
    return;
  }

  var n = parseInt(document.<?= $formname ?>.elements[field].value);
  if (isNaN(n)) {
    document.<?= $formname ?>.elements[field].value = 1;
  }
  else if (n >= 0) {
    document.<?= $formname ?>.elements[field].value = n + 1;
  }
}

function decrement(field)
{
  if (document.<?= $formname ?>.elements[field].disabled) {
    return;
  }

  var n = parseInt(document.<?= $formname ?>.elements[field].value);
  if (isNaN(n)) {
    document.<?= $formname ?>.elements[field].value = 0;
  }
  else if (n >= 1) {
    document.<?= $formname ?>.elements[field].value = n - 1;
  }
}

function pasteTrain()
{
  var si1 = document.reservation.train1_run.selectedIndex;
  if (si1 == 0) {
    return;
  }
  if (si1 < (document.reservation.train1_run.options.length - 1)) {
    document.reservation.train2_date.value = 
      document.reservation.train1_date.value;
    document.reservation.train2_run.selectedIndex = si1 + 1;
  }
  else {
    alert("Sorry, if the outbound train is last one of the day, I'm not sure what to do here. Please fill in the return train.");
  }
}

function disable(elem) { // elem: form element to be disabled
  document.<?= $formname ?>.elements[elem].disabled = true;
}

function enable(elem) { // elem: form element to be reenabled
  document.<?= $formname ?>.elements[elem].disabled = false;
}

//-->
</script>

<form method="POST" name="<?= $formname ?>" enctype="multipart/form-data" action="#">
<input type="hidden" name="actionId" value="" />
<input type="hidden" name="actionName" value="" />


