<?php


use app\Machine\Request;


/**
 * Return the first array key name of request data...
 * e.g. <b>p or page</b>
 */
define('VAR_NAME', array_key_first((new Request())->getData()));

const PREVIOUS_LINK = 'Previous';
const NEXT_LINK     = 'Next';
