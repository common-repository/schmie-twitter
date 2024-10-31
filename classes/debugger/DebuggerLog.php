<?php
/************************************************************************
*
*@file: DebuggerLog.php
*@date: Di. Apr 27 2010
*@author: schmiddi	
*
**************************************************************************/

require_once 'IDebugger.php';



/**
 * class DebuggerLog
 * Writes the Debug Messages in a log file
 */
class DebuggerLog implements IDebugger
{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * handle for the logfile
	 * @access private
	 */
	private $logfile = null;


	/**
	 * Set the path to the logfile
	 *
	 * @param string file 

	 * @return 
	 * @access public
	 */
	public function setLogfile( $file ) {		
		$this->logfile = fopen($file, 'a+');
	} // end of member function setLogfile

	/**
	 * close the file handle
	 * 
	 *
	 * @return 
	 * @access public
	 */
	public function __destruct( ) {		
	#	fclose($this->logfile);
	} // end of member function __destruct



	/**
	 * 
	 *
	 * @param string message 

	 * @return 
	 * @access public
	 */
	public function debug( $message ) {
		
		if ($this->logfile === null)
			$this->setLogfile("/tmp/storableDebug.txt");
		$string = date('j.m H:m:s',time()). "  $message\n";
			
		fwrite($this->logfile, $string);
	} // end of member function debug



} // end of DebuggerLog
?>
