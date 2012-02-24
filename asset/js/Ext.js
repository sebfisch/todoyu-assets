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
 *	Main assets object
 *
 * @class		assets
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.assets = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},



	/**
	 * Initialize assets
	 *
	 * @method	init
	 */
	init: function() {
		this.registerHooks();
	},



	/**
	 * Register JS hooks of assets
	 *
	 * @method	registerHooks
	 */
	registerHooks: function() {
		Todoyu.Hook.add('project.task.edit.cancelled', this.TaskEdit.onCancelledTaskEdit.bind(this.TaskEdit));
		Todoyu.Hook.add('project.task.formLoaded', this.TaskEdit.onTaskEditFormLoaded.bind(this.TaskEdit));
		Todoyu.Hook.add('project.quickTask.closePopup', this.TaskEdit.onCloseQuicktaskPopup.bind(this.TaskEdit));
	},



	/**
	 * Download asset
	 *
	 * @method	download
	 * @param	{Number}	idAsset
	 */
	download: function(idAsset) {
		var params	= {
			action: 'download',
			asset:	idAsset
		};

		Todoyu.goTo('assets', 'asset', params);
	},



	/**
	 * Download (zipped) selection of assets of given task
	 *
	 * @method	downloadSelection
	 * @param	{Number}	idTask
	 */
	downloadSelection: function(idTask) {
		var selectedAssets = this.List.getSelectedAssets(idTask);

		if( selectedAssets.size() === 0 ) {
			Todoyu.notifyError('[LLL:assets.ext.error.minimumFile]');
		} else if( selectedAssets.size() === 1 ) {
			Todoyu.notifyInfo('[LLL:assets.ext.download.normal]');
			this.download(selectedAssets.first());
		} else {
			Todoyu.notifyInfo('[LLL:assets.ext.download.compressed]');
			var params = {
				action:	'download',
				task:		idTask,
				assets:	selectedAssets.join(',')
			};

			Todoyu.goTo('assets', 'zip', params);
		}
	},



	/**
	 * Toggle visibility of an asset
	 *
	 * @method	toggleVisibility
	 * @param	{Number}	idAsset
	 */
	toggleVisibility: function(idAsset) {
		var url		= Todoyu.getUrl('assets', 'asset');
		var options	= {
			parameters: {
				action:	'togglevisibility',
				asset:	idAsset
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Remove given asset
	 *
	 * @method	remove
	 * @param	{Number}	idAsset
	 */
	remove: function(idAsset) {
		if( confirm('[LLL:assets.ext.delete.confirm]') ) {
			var url		= Todoyu.getUrl('assets', 'asset');
			var options	= {
				parameters: {
					action:	'delete',
					asset:	idAsset
				},
				onComplete: this.onRemoved.bind(this, idAsset)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler to be called after having deleted a file: updates file list
	 *
	 * @method	onRemoved
	 * @param	{Number}			idAsset
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(idAsset, response) {
		Effect.Fade('asset-' + idAsset);

		var idTask	= response.getTodoyuHeader('idTask');
		var label	= response.getTodoyuHeader('tabLabel');

		Todoyu.Ext.project.Task.refreshHeader(idTask);

		Todoyu.Notification.notifySuccess('[LLL:assets.ext.delete.notifiy.success]');

		this.setTabLabel(idTask, label);
		this.updateTab(idTask);
	},



	/**
	 * Update assets tab of given task
	 *
	 * @method	updateTab
	 * @param	{Number}		idTask
	 */
	updateTab: function(idTask) {
		var url		= Todoyu.getUrl('assets', 'tasktab');
		var options	= {
			parameters: {
				action:	'tab',
				task:	idTask
			}
		};
		var target	= 'task-' + idTask + '-tabcontent-assets';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Set label of task assets tab
	 *
	 * @method	setTabLabel
	 * @param	{Number}	idTask
	 * @param	{String}	label
	 */
	setTabLabel: function(idTask, label) {
		$('task-' + idTask + '-tab-assets-label').select('.labeltext').first().update(label);
	},



	/**
	 * Toggle assets list visibility
	 *
	 * @method	toggleList
	 * @param	{Number}		idTask
	 */
	toggleList: function(idTask) {
		this.List.toggle(idTask);
	},



	/**
	 * Add new asset to given task: expand task details and open assets tab with new asset form
	 *
	 * @method	addTaskAsset
	 * @param	{Number}	idTask
	 */
	addTaskAsset: function(idTask) {
		Todoyu.Ext.project.Task.showDetails(idTask, 'assets', this.onTaskAssetTabLoaded.bind(this));
	},



	/**
	 * Handler when task asset tab is loaded: show asset upload form
	 *
	 * @method	onTaskAssetTabLoaded
	 * @param	{Number}	idTask
	 * @param	{String}	tab
	 */
	onTaskAssetTabLoaded: function(idTask, tab) {
		if( ! Todoyu.exists('formElement-asset-' + idTask + '-field-file') ) {
			this.Upload.showForm(idTask);
		}
	}

};