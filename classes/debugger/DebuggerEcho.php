<?php
/************************************************************************
*
*@file: DebuggerEcho.php
*@date: Di. Apr 27 2010
*@author: schmiddi	
*
**************************************************************************/

require_once 'IDebugger.php';


/**
 * class DebuggerEcho
 * writes debug messages on the screen buffer
 */
class DebuggerEcho implements IDebugger
{
	/**
	 * 
	 *
	 * @param string message 

	 * @return nothing
	 * @access public
	 */
	public function debug( $message ) {
		echo date('j.m H:m:s',time()). "  $message <br>";
	} // end of member function debug



} // end of DebuggerEcho
?>
