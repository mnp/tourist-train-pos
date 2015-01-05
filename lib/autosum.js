<SCRIPT TYPE="text/javascript">
<!--
function formatCurrency(num) {

  // num = num.toString().replace(/\$|\,/g,''));

  if(isNaN(num))
    num = "0";
  sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
  cents = num%100;
  num = Math.floor(num/100).toString();
  if(cents<10)
    cents = "0" + cents;
  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
    num = num.substring(0,num.length-(4*i+3))+','+
      num.substring(num.length-(4*i+3));
  return (((sign)?'':'-') 
	  //+ '$' 
	  + num + '.' + cents);
}

function autoSumFloat(src, dest, dest2)
{
  sv = parseFloat(src.value);
  dv = parseFloat(dest.value);
  if (isNaN(sv) || isNaN(dv)) {
    return;
  }
  src.value  = formatCurrency(sv);
  dest.value = formatCurrency(sv + dv);
}

function autoSumInt(src, dest)
{
  if (((src.value / src.value) != 1) && (src.value != 0)) {
    return;
  }
  else {    
    dest.value = 
        parseInt((src.value == '')  ? 0 : src.value)
      + parseInt((dest.value == '') ? 0 : dest.value);
  }
}
//-->
</SCRIPT>
