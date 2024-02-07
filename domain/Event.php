<?php
/**
 * Encapsulated version of a dbEvents entry.
 */
class Event {
    private $id;
    private $eventname;

    function __construct($id, $eventname) {
        $this->id = $id;
        $this->eventname = $name;
    }

    function getID() {
        return $this->id;
    }

    function getName() {
        return $this->eventname;
    }
}
