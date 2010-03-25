<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Assets (JS, CSS, SWF, etc.) requirements for assets extension
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
Todoyu::$CONFIG['EXT']['assets']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/assets/assets/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/assets/assets/js/List.js',
			'position'	=> 110
		),
		array(
			'file'		=> 'ext/assets/assets/js/Upload.js',
			'position'	=> 120
		),
		array(
			'file'		=> 'lib/js/md5.js',
			'position'	=> 26
		)
	),
	'css' => array(
		array(
			'file'	=> 'ext/assets/assets/css/ext.css'
		),
		array(
			'file'	=> 'ext/assets/assets/css/mime.css'
		)
	)
);

?>