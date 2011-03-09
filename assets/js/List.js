/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * @module	Assets
 */

/**
 * List assets
 *
 * @class		List
 * @namespace	Todoyu.Ext.assets
 */
Todoyu.Ext.assets.List = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.assets,



	/**
	 * Toggle display of assets list of given task
	 *
	 * @method	toggle
	 * @param	{Number}		idTask
	 */
	toggle: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-assets-list');
	},



	/**
	 * Refresh assets list of given task
	 *
	 * @method	refresh
	 * @param	{Number}	idTask
	 */
	refresh: function(idTask) {
		var list	= 'task-' + idTask + '-assets-list';
		var url		= Todoyu.getUrl('assets', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'list',
				'task':		idTask
			}
		};

		if( Todoyu.exists(list) ) {
			Todoyu.Ui.replace(list, url, options);
		} else {
			var target	= 'task-' + idTask + '-tabcontent-assets';
			Todoyu.Ui.update(target, url, options);
		}
	},



	/**
	 * Hover given asset
	 *
	 * @method	hover
	 * @param	{Number}	idAsset
	 */
	hover: function(idAsset) {
		$('asset-' + idAsset).toggleClassName('hover');
	},



	/**
	 * Select given asset
	 *
	 * @method	select
	 * @param	{Number}	idAsset
	 */
	select: function(idAsset) {
		if( $('asset-' + idAsset + '-checkbox').checked ) {
			this.unCheck(idAsset);
		} else {
			this.check(idAsset);
		}
	},



	/**
	 * Set given asset checked
	 *
	 * @method	check
	 * @param	{Number}	idAsset
	 */
	check: function(idAsset) {
		$('asset-' + idAsset).addClassName('selected');
		$('asset-' + idAsset + '-checkbox').checked = true;
	},



	/**
	 * Set asset unchecked
	 *
	 * @method	unCheck
	 * @param	{Number}	idAsset
	 */
	unCheck: function(idAsset) {
		$('asset-' + idAsset).removeClassName('selected');
		$('asset-' + idAsset + '-checkbox').checked = false;
	},



	/**
	 * Select all assets of given task
	 *
	 * @method	selectAll
	 * @param	{Number}	idTask
	 */
	selectAll: function(idTask) {
		var list 	= $('task-' + idTask + '-assets-tablebody');
		var boxes	= list.select('input');

			// All already checked?
		var notAll = false;
		boxes.each(function(item){
			if( ! item.checked ) {
				notAll = true;
				return;
			}
		});

		boxes.each(function(item){
			if( notAll === true ) {
				this.check(item.value);
			} else {
				this.unCheck(item.value);
			}
		}.bind(this));

		$('task-' + idTask + '-assets-checkallbox').checked = notAll;
	},



	/**
	 * Get selected assets of given task
	 *
	 * @method	getSelectedAssets
	 * @param	{Number}	idTask
	 * @return	Array
	 */
	getSelectedAssets: function(idTask) {
		var list 	= $('task-' + idTask + '-assets-tablebody');
		var boxes	= list.select('input');
		var assetIDs= [];

		boxes.each(function(item){
			if( item.checked ) {
				assetIDs.push(item.value);
			}
		});

		return assetIDs;
	}

};