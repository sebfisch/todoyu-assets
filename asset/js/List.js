/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
	 * Initialize list
	 *
	 * @method	initList
	 * @param	{Number}	idTask
	 */
	initList: function(idTask) {
		this.addListObservers(idTask);
	},



	/**
	 * Initialize control
	 *
	 * @method	initControl
	 * @param	{Number}	idTask
	 */
	initControl: function(idTask) {
		this.toggleButtons(idTask);
	},



	/**
	 * Toggle control buttons
	 * Hide download selection if no files available
	 *
	 * @method	toggleButtons
	 * @param	{Number}	idTask
	 */
	toggleButtons: function(idTask) {
		var method	= this.hasListElements(idTask) ? 'show' : 'hide';

		$('task-'+ idTask + '-asset-button-downloadselection')[method]();
	},



	/**
	 * Check whether list exists and contains elements
	 *
	 * @method	hasListElements
	 * @param	{Number}	idTask
	 * @return	{Boolean}
	 */
	hasListElements: function(idTask) {
		var list = $('task-' + idTask + '-assets-list');

		if( list ) {
			return !!list.down('tbody tr');
		}

		return false;
	},



	/**
	 * Add observers for list
	 *
	 * @method	addObservers
	 * @param	{Number}	idTask
	 */
	addListObservers: function(idTask) {
			// Check all button
		$('task-' + idTask + '-assets-checkallbox').on('click', this.toggleSelectAll.bind(this, idTask));

			// Select asset row
		var assetsTableBody	= $('task-' + idTask + '-assets-tablebody');
		assetsTableBody.on('click', 'tr', this.select.bind(this));

			// Actions
		assetsTableBody.select('tr').each(function(row){
			var idAsset	= row.id.split('-').last();
				// Filename
			row.down('.filename a').on('click', 'td', this.handleDownloadClick.bind(this, idAsset));
				// Visibility
			if( row.down('a.visibility') ) {
				row.down('a.visibility').on('click', 'a', this.handleVisibilityToggle.bind(this, idAsset));
			}
				// Download
			if( row.down('a.download') ) {
				row.down('a.download').on('click', 'td', this.handleDownloadClick.bind(this, idAsset));
			}
				// Delete
			if( row.down('a.delete') ) {
				row.down('a.delete').on('click', 'td', this.handleRemoveClick.bind(this, idAsset));
			}
		}, this);
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
			parameters: {
				action:	'list',
				task:	idTask
			},
			onComplete: this.onRefreshed.bind(this, idTask)
		};

		if( Todoyu.exists(list) ) {
			Todoyu.Ui.replace(list, url, options);
		} else {
			var target	= 'task-' + idTask + '-tabcontent-assets';
			Todoyu.Ui.insert(target, url, options);
		}
	},



	/**
	 * Re-init after refresh
	 *
	 * @method	onRefreshed
	 * @param	{Number}		idTask
	 * @param	{Ajax.Response}	response
	 */
	onRefreshed: function(idTask, response) {
		this.initControl(idTask);
	},



	/**
	 * Select given asset
	 *
	 * @method	select
	 * @param	{Event}		event
	 * @param	{Element}	row
	 */
	select: function(event, row) {
		var idAsset	= $(row).id.split('-').last();

		if( row.hasClassName('selected') ) {
			this.unCheck(idAsset);
		} else {
			this.check(idAsset);
		}
	},



	/**
	 * Toggle given asset visibility (hide from customers?)
	 *
	 * @method	handleVisibilityToggle
	 * @param	{Number}	idAsset
	 * @param	{Event}		event
	 * @param	{Element}	link
	 */
	handleVisibilityToggle: function(idAsset, event, link) {
		event.stop();

		link.toggleClassName('not');
		this.ext.toggleVisibility(idAsset);
	},



	/**
	 * Download handler when clicking on a filename
	 *
	 * @method	handleDownloadClick
	 * @param	{Number}	idAsset
	 * @param	{Event}		event
	 * @param	{Element}	cell
	 */
	handleDownloadClick: function(idAsset, event, cell) {
		event.stop();

		this.ext.download(idAsset);
	},



	/**
	 * Handle download click
	 *
	 * @method	handleRemoveClick
	 * @param	{Number}	idAsset
	 * @param	{Event}		event
	 * @param	{Element}	cell
	 */
	handleRemoveClick: function(idAsset, event, cell) {
		event.stop();

		this.ext.remove(idAsset);
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

		var idTask = $('asset-' + idAsset).up('tbody').id.split('-')[1];

		this.toggleSelectionDownload(idTask);
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

		var idTask = $('asset-' + idAsset).up('tbody').id.split('-')[1];

		this.toggleSelectionDownload(idTask);
	},



	/**
	 * Toggle status of download selection button depending on the amount of selected assets
	 * Disable if no asset is selected
	 *
	 * @method	toggleSelectionDownload
	 * @param	{Number}	idTask
	 */
	toggleSelectionDownload: function(idTask) {
		var oneSelected = $('task-' + idTask + '-assets-tablebody').select(':checkbox:checked').size() > 0;
		var method		= oneSelected ? 'enable' : 'disable';

		$('task-' + idTask + '-asset-button-downloadselection')[method]();
	},



	/**
	 * Get assets checkbox elements of given task
	 *
	 * @method	getAllAssetsCheckboxes
	 * @param	{Number}	idTask
	 * @param	{Boolean}	checkedOnly
	 * @return	{Element[]}
	 */
	getAssetsCheckboxes: function(idTask, checkedOnly) {
		checkedOnly	= checkedOnly ? checkedOnly : false;

		var list	= $('task-' + idTask + '-assets-tablebody');

		var selector	= checkedOnly ? 'input:checked' : 'input';

		return list.select(selector);
	},



	/**
	 * Get selected assets of given task
	 *
	 * @method	getSelectedAssets
	 * @param	{Number}	idTask
	 * @return	{Number[]}	Asset IDs
	 */
	getSelectedAssets: function(idTask) {
		var boxes	= this.getAssetsCheckboxes(idTask, true);

		return boxes.collect(function(box) {
			return box.value;
		});
	},



	/**
	 * Check whether all assets of the given task are selected
	 *
	 * @method	areAllAssetsSelected
	 * @param	{Number}	idTask
	 */
	areAllAssetsSelected: function(idTask) {
		var boxes	= this.getAssetsCheckboxes(idTask, false);

		return boxes.all(function(box){
			return box.checked
		});
	},



	/**
	 * De/Select all assets of given task
	 *
	 * @method	selectAll
	 * @param	{Number}	idTask
	 * @param	{Event}		event
	 */
	toggleSelectAll: function(idTask, event) {
		var allChecked	= this.areAllAssetsSelected(idTask);
		var boxes		= this.getAssetsCheckboxes(idTask, false);

		boxes.each(function(item){
			if( allChecked !== true ) {
				this.check(item.value);
			} else {
				this.unCheck(item.value);
			}
		}, this);

		$('task-' + idTask + '-assets-checkallbox').checked = ! allChecked;
	}

};