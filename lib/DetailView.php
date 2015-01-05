<?php

require_once 'View.php';

/**
* DetailView - container for template operations
*
* @access public
* @package DetailView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class DetailView extends View
{
  /**
   * Title (not name) of table.
   *
   * @var     string
   * @access  private
   */
  var $table_name;

  /**
   * The table data
   *
   * @var     array 	[title] => data
   * @access  private
   */
  var $items = array();

  /**
   * Javascript code to perform clear.
   *
   * @var     string
   * @access  private
   */
  var $clearCode = '';

  /**
   * Template to use for display.
   *
   * @var     string
   * @access  private
   */
  var $template = 'detail.html';

  function getPopupLink($topic)
  {
    return '<a href="' . ROOTURL . '/lib/HelpLoader.php" ' 
      . "onClick=\"return popup(this, '{$topic}')\">"
      . '<img src="' . ROOTURL . '/graphics/help.gif">'
      . '</a>';
  }

  /**
   * @access public
   * @return string HTML management table
   */
  function toHtml($show_find=true)
  {
    if ($this->editable) {
      $this->help_link = MNP::popupHelpLink($this->table_name);

      // wrap the clearing code in a JS function
      $this->clearCode = 
	MNP::wrapJavascript( "function clear{$this->table_name} () {"
			     . $this->clearCode
			     . "}"
			     );

      $this->actions[] = MNP::clear_table($this->table_name, 'Clear Form');
      if ($show_find) {
	$this->actions[] = MNP::action($this->formname, 'find', 'Find');
      }
    }
    else {      
      $this->help_link = '';      
    }
    
    return MNP::bufferedOutputTemplate($this->template, $this);
  }

  /**
   * If the object has them, set up its lastMod fields in object for display
   *
   * @access private
   */
  function prepareLastMods()
  {
    if (isset($this->data_object->lastModUid)) {
      $u = DataObjects_TUser::staticGet($this->data_object->lastModUid);
      $this->lastModUserName = $u->user_uid;
      $this->lastModDateTime = $this->data_object->lastModDateTime;      
    }
    if (isset($this->data_object->created)) {
      $this->created = $this->data_object->created;      
    }
  }


  /**
   * Mindless key/value displayer
   *
   * @access public
   */
  function setDisplayItems($editAction, $use_nice=true)
  {
    assert($this->data_object);
    $do = &$this->data_object;

    $this->prepareLastMods();

    foreach ($do->_get_table() as $fieldname=>$type)
    {
      // all fields with nicenames are shown in detail/edit views
      $nn = $do->niceName($fieldname);
      if (!$nn) {
	continue;
      }

      if (!$use_nice) { 
	$nn = $fieldname; 
      }
      
      $value = @$do->$fieldname;
      
      // If there's an input string for this field in the DataObject, use it
      // to generate the HTML input string, otherwise guess on the type.
      if (method_exists($do, 'makeDisplayItem')) {
	$instr = $do->makeDisplayItem($fieldname, $value);
      }

      if (@$instr) {
	$this->items[$nn] = $instr;
      }
      else if ($type == DB_DATAOBJECT_BOOL) {
	$this->items[$nn] = $value ? "yes" : "no";
      }
      else {	
	$this->items[$nn] = $value;
      }
    }
    $this->actions[] = $editAction;
  }
  

  /**
   * This ambitious function sets the item array in a DetailView object
   * to have all the field prompts and values.  
   *
   * @param bool use_nice
   * @access public
   */
  function setEditItems($use_nice=true)
   {
    assert($this->data_object);
    $do = &$this->data_object;

    $this->prepareLastMods();

    foreach ($do->_get_table() as $fieldname=>$type)
    {
      // all fields with nicenames are shown in detail/edit views
      $nicename = $do->niceName($fieldname);
      if (!$nicename) {
	continue;
      }
      $nn = $use_nice ? $nicename : $fieldname;

      // default values come from $_POST, via a setFrom call in controller
      $value = @$do->$fieldname;

      // Required fields have their prompts highlighted
      if (method_exists($do, 'requiredField')) {
	if ($do->requiredField($fieldname)) {
	  $nn = '<font color="red">' . $nn . '</font>';
	}
      }

      // If there's an input string for this field in the DataObject, use it
      // to generate the HTML input string, otherwise guess on the type.
      if (method_exists($do, 'makeInputItem')) {
	$instr = $do->makeInputItem($fieldname, $value);
      }          

      if (@$instr) {
	$this->items[$nn] = $instr;
      }
      elseif ($type == DB_DATAOBJECT_BOOL) {
	$this->items[$nn] = MNP::input_bool($fieldname, $value);	  
      }
      elseif ($type == DB_DATAOBJECT_INT) {
	$this->items[$nn] = MNP::input_number($fieldname, $value);
      }
      elseif ($type == DB_DATAOBJECT_STR) {
	$this->items[$nn] = MNP::input_string($fieldname, $value);
      }
      elseif ($fieldname == 'tStation_id') {
	$this->items[$nn] = DataObjects_TStation::staticFormCode($fieldname,
								 $value);
      }
      elseif ($fieldname == 'date') {
	// DataObject has a date type but it's not working yet
	$this->items[$nn] = MNP::date_button_string($this->formname, 
						    $fieldname, $value);
      }
      elseif ($fieldname == 'comment') {
	$this->items[$nn] = MNP::input_comment($fieldname, $value);
      }
      else {
	assert("bad type");
      }

      // Dataobjects can specify some clearing code for a field
      $js =  method_exists($do, 'makeClear')
	? $do->makeClear($fieldname, $this->formname)
	: null;

      // Append the Javascript code to clear the field
      $this->clearCode .= is_null($js)
	? "document.{$this->formname}.{$fieldname}.value = \"\";\n"
	: $js;
    }

    if (method_exists($do, 'makeArbCode')) {
      $this->arbCode = $do->makeArbCode($this->formname);
    }
  }
}
?>