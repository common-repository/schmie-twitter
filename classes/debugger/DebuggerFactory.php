<?php
/************************************************************************
*
*@file: DebuggerFactory.php
*@date: Di. Apr 27 2010
*@author: schmiddi	
*
**************************************************************************/


require_once 'DebuggerLog.php';
require_once 'DebuggerNull.php';
require_once 'DebuggerEcho.php';


/**
 
 * class DebuggerFactory
 * delivers exactly one object from each class which implements the interface
 * IDebugger.
 * Example call:$debugger =DebuggerFactory::deliver(DebuggerFactory::D_LOG);
 
 */
class DebuggerFactory
{
	
	 /*** Attributes: ***/

	/**
	 * An array of Objects wich are derived from the Interface IDebugger
	 * @static
	 * @access private
	 */
	private static $items=array();

	/**	
	 * @var constant
	 * @desc alias for a DebuggerEcho
	 */
	const D_ECHO = 'DebuggerEcho';
	/**	
	 * @var constant
	 * @desc alias for a DebuggerLog
	 */
	const D_LOG = 'DebuggerLog';	
	/**	
	 * @var constant
	 * @desc alias for a DebuggerNull (no debug output
	 */
	const D_NULL = 'DebuggerNull';
	/**
	 * 
	 *
	 * @param string item Name of the desired Debugger


	 * @return IDebugger
	 * @static
	 * @access public
	 */
	public static function deliver( $item ) {
		if (empty(self::$items[$item]))
			self::$items[$item] = new $item();
		return self::$items[$item];
	} // end of member function deliver





} // end of DebuggerFactory
?>
