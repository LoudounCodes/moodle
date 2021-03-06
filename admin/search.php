<?php

// searches for admin settings

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$query = trim(optional_param('query', '', PARAM_NOTAGS));  // Search string

$PAGE->set_context(context_system::instance());

admin_externalpage_setup('search', '', array('query' => $query)); // now hidden page

$adminroot = admin_get_root(); // need all settings here
$adminroot->search = $query; // So we can reference it in search boxes later in this invocation
$statusmsg = '';
$errormsg  = '';
$focus = '';

// now we'll deal with the case that the admin has submitted the form with changed settings
if ($data = data_submitted() and confirm_sesskey()) {
    if (admin_write_settings($data)) {
        redirect($PAGE->url, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    if (!empty($adminroot->errors)) {
        $errormsg = get_string('errorwithsettings', 'admin');
        $firsterror = reset($adminroot->errors);
        $focus = $firsterror->id;
    } else {
        redirect($PAGE->url);
    }
}

// and finally, if we get here, then there are matching settings and we have to print a form
// to modify them
echo $OUTPUT->header($focus);

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

echo admin_search_settings_html($query);

echo $OUTPUT->footer();
