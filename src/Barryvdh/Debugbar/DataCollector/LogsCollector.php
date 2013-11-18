<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\MessagesCollector;

class LogsCollector extends MessagesCollector
{
    
    public function __construct($name = 'logs')
    {
        $this->name = $name;
        $logs=$this->getStorageLogs();
         foreach($logs as &$log){
            $level= $log['level'];
            unset($log['level']);
            $this->addMessage( $log,$level );
         }
        
    }
 
    
    /**
     * get logs apache in app/storage/logs 
     * only 24 last of current day
     *
     * @return Array
     */
    private function getStorageLogs($max=24)
    {
	$file=
	$log = array();
	$pattern = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}.*/";
	$log_levels=array(
	    'emergency' => 'EMERGENCY',
	    'alert'     => 'ALERT',
	    'critical'  => 'CRITICAL',
	    'error'     => 'ERROR',
	    'warning'   => 'WARNING'
	    //'notice'  => 'NOTICE',
	    //'info'    => 'INFO',
	    //'debug'   => 'DEBUG'
	);
	$log_file = app_path().'/storage/logs/log-'.php_sapi_name().'-'.date('Y-m-d').'.txt';
 	if ( file_exists($log_file)) {
	    $file = \File::get($log_file);
 
	    preg_match_all($pattern, $file, $headings);
	    $log_data = preg_split($pattern, $file);
 
	    unset($log_data[0]);            
	    foreach ($headings as $h) {
		for ($i=0; $i < count($h); $i++) {
		    foreach ($log_levels as $ll) {
			if (strpos(strtolower($h[$i]), strtolower('log.' . $ll))) {
			    $log[$i+1] = array('level' => $ll, 'header' => $h[$i], 'stack' => $log_data[$i+1]);
			}
		    }
		}
	    }
	}
 
	unset($headings);
	unset($log_data);
	$log = array_slice( array_reverse($log), 0, $max);//news to old and 24 max
	return $log;
    }    
    
}