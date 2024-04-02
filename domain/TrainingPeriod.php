<?php
/**
 * Encapsulated version of a dbTrainingPeriod entry.
 * not 100% certain how this is used yet but the other dbs had it so I'll probably need it at some point
 */
class TrainingPeriod {
    private $id;
    private $name;
    private $startdate;
    private $enddate;

    function __construct($id, $name, $startdate, $enddate) {
        $this->id = $id;
        $this->name = $name;
        $this->startdate = $startdate;
        $this->enddaate = $enddate;

    }

    function get_id() {
        return $this->id;
    }

    function get_name() {
        return $this->eventname;
    }

    function get_startdate(){
        return $this->startdate;
    }
    
    function get_enddate(){
        return $this->enddate;
    }
}
