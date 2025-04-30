<?php

function codingtask_update_grades($task, $grades = null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    
    if ($grades) {
        grade_update('mod/codingtask', $task->course, 'mod', 'codingtask',
                     $task->id, 0, $grades);
    } else {
        grade_update('mod/codingtask', $task->course, 'mod', 'codingtask',
                     $task->id, 0, null);
    }
}

function codingtask_add_instance($data, $mform = null) {
    global $DB;
    $record = new stdClass();
    $record->name = $data->name;
    $record->description = $data->description;
    $record->input = $data->input;
    $record->expectedoutput = $data->expectedoutput;
    $record->language = $data->language ?? 'cpp';
    $record->timecreated = time();

    return $DB->insert_record('codingtask', $record);
}
// we update the coding task here
function codingtask_update_instance($data, $mform = null) {
    global $DB;
    $record = new stdClass();
    $record->id = $data->instance;
    $record->name = $data->name;
    $record->description = $data->description;
    $record->input = $data->input;
    $record->expectedoutput = $data->expectedoutput;
    $record->language = $data->language ?? 'cpp';

    return $DB->update_record('codingtask', $record);
}

//will compile code via api and findout the result
function codingtask_complile_code($userid,$taskid,$taskinput,$taskoutput,$code)
{
    global $DB;
    // pass source code and sample input to evaluator
    $result = mod_codingtask\evaluator::run_code($code, $taskinput);

    //evaluator return the result response and we will show either the source code output match with our sample output
    $output = $result['run']['stdout'] ?? $result['run']['stderr'] ?? $result['compile']['stderr'] ?? 'No output';
    $output_lines = array_map('trim', explode("\n", trim($output)));
    $expected_lines = array_map('trim', explode("\n", trim($taskoutput)));
    $is_correct = ($output_lines === $expected_lines) ? 1 : 0;

    $record = new stdClass();
    $record->codingtaskid = $taskid;
    $record->userid = $userid;
    $record->code = $code;
    $record->output = $output;
    $record->is_correct = $is_correct;
    $record->timecreated = time();

    $DB->insert_record('codingtask_submissions', $record);

    echo "<h4>Output:</h4><pre>$output</pre>";
    echo $is_correct ? "<p style='color:green;'>✅ accepted!</p>" : "<p style='color:red;'>❌ Output mismatch.</p>";
}

// will show all our previous submitted response
function codingtask_display_previous_submissions($userid, $taskid)
{
    global $DB;
    $submissions = $DB->get_records('codingtask_submissions', ['userid' => $userid, 'codingtaskid' => $taskid]);
    if ($submissions) {
        echo "<h4>Your Latest Submissions:</h4>";
        foreach (array_reverse($submissions) as $sub) {
            echo "<div style='border:1px solid #ccc; margin-bottom:10px; padding:5px;'>";
            echo "<strong>Submitted at:</strong> " . userdate($sub->timecreated) . "<br>";
            echo "<strong>Correct:</strong> " . ($sub->is_correct ? '✅' : '❌') . "<br>";
            echo "<strong>Output:</strong><pre>{$sub->output}</pre>";
            echo "</div>";
        }
    }
}
