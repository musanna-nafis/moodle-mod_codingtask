<?php
require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/codingtask/classes/evaluator.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('codingtask', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$task = $DB->get_record('codingtask', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/codingtask/view.php', array('id' => $id));
$PAGE->set_title(format_string($task->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($task->name));

$submitted_code = isset($_POST['code']) ? htmlspecialchars($_POST['code']) : '';
echo '<form method="post">
    <textarea name="code" rows="10" cols="80" placeholder="Write your code here...">' . $submitted_code . '</textarea><br>
    <button type="submit">Submit</button>
</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    global $USER;
    $code = $_POST['code'];
    echo "<h3>✅ Submitted Code:</h3><pre>$code</pre>";
    echo "<h3>🚀 Input:</h3><pre>$task->input</pre>";

    $result = mod_codingtask\evaluator::run_code($code, $task->input);

    // DEBUG: Show full Judge0 API response
    echo "<h3>🔍 Raw API Response:</h3>";
    echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
    echo "<h4>🚀 Task Input:</h4><pre>" . htmlspecialchars($task->input) . "</pre>";
    $output = $result['run']['stdout'] ?? $result['run']['stderr'] ?? $result['compile']['stderr'] ?? 'No output';
    $is_correct = (trim($output) === trim($task->expectedoutput)) ? 1 : 0;

    $record = new stdClass();
    $record->codingtaskid = $task->id;
    $record->userid = $USER->id;
    $record->code = $code;
    $record->output = $output;
    $record->is_correct = $is_correct;
    $record->timecreated = time();

    $DB->insert_record('codingtask_submissions', $record);

    echo "<h4>Output:</h4><pre>$output</pre>";
    echo $is_correct ? "<p style='color:green;'>✅ Output matches!</p>" : "<p style='color:red;'>❌ Output mismatch.</p>";
}

$submissions = $DB->get_records('codingtask_submissions', ['userid' => $USER->id, 'codingtaskid' => $task->id]);
if ($submissions) {
    echo "<h4>Your Submissions:</h4>";
    foreach ($submissions as $sub) {
        echo "<div style='border:1px solid #ccc; margin-bottom:10px; padding:5px;'>";
        echo "<strong>Submitted at:</strong> " . userdate($sub->timecreated) . "<br>";
        echo "<strong>Correct:</strong> " . ($sub->is_correct ? '✅' : '❌') . "<br>";
        echo "<strong>Output:</strong><pre>{$sub->output}</pre>";
        echo "</div>";
    }
}

echo $OUTPUT->footer();