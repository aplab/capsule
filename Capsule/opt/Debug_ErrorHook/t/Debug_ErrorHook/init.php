<?php
chdir(dirname(__FILE__));
error_reporting(E_ALL);
ini_set('track_errors', 1);

require_once "../../lib/config.php";
require_once "Debug/ErrorHook/Listener.php";
require_once "Debug/ErrorHook/INotifier.php";


function printr($value, $comment=null)
{
    if ($comment !== null) echo "$comment: ";
    var_export($value);
    echo "\n";
    ob_flush(); // essential! else above echo may be flushed AFTER a notice to STDERR
}

function cleanupStdout($s)
{
	$s = preg_replace("/((?:in|at) \[?).*?( on line |:)\d+/s", "$1*$2*", $s);
	$s = preg_replace("/(\#\d \s*) \S+ \(\d+\)/sx", '$1*(*)', $s);
	return $s;
}

function error_handler($errno, $errstr, $errfile, $errline)
{
    if ($errno & error_reporting()) {
        echo "Error [$errno]: $errstr in $errfile on line $errline\n";
    }
    return true; // skip built-in error handler
}

class PrintNotifier implements Debug_ErrorHook_INotifier
{
	public function notify($errno, $errstr, $errfile, $errline, $trace, $hash = null, $prependText = null)
	{
		printr(
			array(
				"errno" => $errno,
				"errstr" => $errstr,
				"errfile" => basename($errfile),
				"errline" => "*",
				"tracecount" => count($trace)
			) + ($prependText? array("prependText" => $prependText) : array()),
			"Notification"
		);
	}
}


if (function_exists("xdebug_disable")) xdebug_disable();
ob_start("cleanupStdout");
set_error_handler('error_handler');
