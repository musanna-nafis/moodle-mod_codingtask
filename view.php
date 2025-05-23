<?php
require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/codingtask/classes/evaluator.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('codingtask', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$task = $DB->get_record('codingtask', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
$PAGE->set_url(new moodle_url('/mod/codingtask/view.php'), array('id' => $id));
$PAGE->set_title(format_string($task->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($task->name));

$submitted_code = isset($_POST['code']) ? htmlspecialchars($_POST['code']) : '';
echo '<form method="post">
    <textarea name="code" rows="10" cols="80" placeholder="Write your code here...">' . $submitted_code . '</textarea><br>
    <label for="language"> Select Language:</label>
    <select name="language" id="language">
    <option value="cpp"> C++ </option>
    <option value="java"> Java</option>
    <option value="python">Python</option>
    <option value="php">PHP</option>
    <option value="csharp">c#</option>
    </select>
    <button type="submit">Submit</button>
</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    global $USER;
    $code = $_POST['code'];
    $language=$_POST['language'];
    //compile and showing result
    codingtask_complile_code($USER->id,$task->id,$task->input,$task->expectedoutput,$code,$language);
}

// will show all our previous submitted response
codingtask_display_previous_submissions($USER->id,$task->id);

echo $OUTPUT->footer();