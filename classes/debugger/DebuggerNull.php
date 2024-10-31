<?php
/************************************************************************
*
*@file: DebuggerNull.php
*@date: Di. Apr 27 2010
*@author: schmiddi	
*
**************************************************************************/

require_once 'IDebugger.php';


/**
 * class DebuggerNull
 * Writes nothing (like ls  > /dev/null)
 */
class DebuggerNull implements IDebugger
{
	/**
	 * 
	 *
	 * @param string message 

	 * @return 
	 * @access public
	 */
	public function debug( $message ) {
		;
	} // end of member function debug



} // end of DebuggerNull
?>
