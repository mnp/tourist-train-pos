<?php
/*
 * $Horde: nag/menu.php,v 1.6 2002/01/24 20:01:59 chuck Exp $
 *
 * Copyright 2001 Jon Parise <jon@horde.org>
 *
 * See the enclosed file COPYING for license information (GPL). If you did
 * not receive such a file, see also http://www.fsf.org/copyleft/gpl.html.
 */

require_once HORDE_BASE . '/lib/Menu.php';

require ADMIN_TEMPLATES . '/menu/menu.inc';

/* Include the JavaScript for the help system (if enabled). */
if ($conf['user']['online_help'] && $browser->hasFeature('javascript')) {
    Help::javascript();
}

?>
