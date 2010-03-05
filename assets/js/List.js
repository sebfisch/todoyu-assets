/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * List assets
 */
Todoyu.Ext.assets.List = {

	ext: Todoyu.Ext.assets,



	/**
	 * Toggle display of assets list of given task
	 * 
	 * @param	Integer		idTask
	 */
	toggle: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-assets-list');		
	},



	/**
	 * Refresh assets list of given task
	 *
	 * @param	Integer	idTask
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
	 * @param	Integer	idAsset
	 */
	hover: function(idAsset) {
		$('asset-' + idAsset).toggleClassName('hover');
	},



	/**
	 * Select given asset
	 *
	 * @param	Integer	idAsset
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
	 * @param	Integer	idAsset
	 */
	check: function(idAsset) {
		$('asset-' + idAsset).addClassName('selected');
		$('asset-' + idAsset + '-checkbox').checked = true;
	},



	/**
	 * Set asset unchecked
	 *
	 * @param	Integer	idAsset
	 */
	unCheck: function(idAsset) {
		$('asset-' + idAsset).removeClassName('selected');
		$('asset-' + idAsset + '-checkbox').checked = false;
	},



	/**
	 * Select all assets of given task
	 *
	 * @param	Integer	idTask
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

		var checkAll = notAll ? true : false ;

		boxes.each(function(item){
			if( checkAll === true ) {
				this.check(item.value);
			} else {
				this.unCheck(item.value);
			}
		}.bind(this));

		$('task-' + idTask + '-assets-checkallbox').checked = checkAll;
	},



	/**
	 * Get selected assets of given task
	 *
	 * @param	Integer	idTask
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