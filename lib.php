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
