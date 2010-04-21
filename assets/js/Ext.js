/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 *	Ext: assets
 */

Todoyu.Ext.assets = {

	PanelWidget: {},

	Headlet: {},



	/**
	 * Download asset
	 *
	 * @param	{Integer}	idAsset
	 */
	download: function(idAsset) {
		var params	= {
			'action': 'download',
			'asset': idAsset
		};

		Todoyu.goTo('assets', 'asset', params);
	},



	/**
	 * Download (zipped) selection of assets of given task
	 *
	 * @param	{Integer}	idTask
	 */
	downloadSelection: function(idTask) {
		var selectedAssets = this.List.getSelectedAssets(idTask);
				
		if( selectedAssets.size() === 0 ) {
			Todoyu.notifyError('[LLL:assets.error.minimumFile]', 3);
		} else if( selectedAssets.size() === 1 ) {
			Todoyu.notifyInfo('[LLL:assets.download.normal]', 3);
			this.download(selectedAssets.first());
		} else {
			Todoyu.notifyInfo('[LLL:assets.download.compressed]', 3);
			var params = {
				'action':   'download',
				'task':     idTask,
				'assets':   selectedAssets.join(',')
			};

			Todoyu.goTo('assets', 'zip', params);
		}
	},



	/**
	 * Remove given asset
	 *
	 * @param	{Integer}	idAsset
	 */
	remove: function(idAsset) {
		if( confirm('[LLL:assets.delete.confirm]') ) {
			var url		= Todoyu.getUrl('assets', 'asset');
			var options	= {
				'parameters': {
					'action':	'delete',
					'asset':	idAsset
				},
				'onComplete': this.onRemoved.bind(this, idAsset)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler to be called after having deleted a file: updates file list
	 * 
	 * @param	{Integer}	idAsset
	 * @param	{Object}	response
	 */
	onRemoved: function(idAsset, response) {
		Effect.Fade('asset-' + idAsset);

		var idTask	= response.getTodoyuHeader('idTask');
		var label	= response.getTodoyuHeader('tabLabel');

		this.setTabLabel(idTask, label);
		this.updateTab(idTask);
	},



	/**
	 * Toggle given asset visibility (hide from customers?)
	 *
	 * @param	{Integer} 	idAsset
	 */
	toggleVisibility: function(idAsset) {
		var url		= Todoyu.getUrl('assets', 'asset');
		var options	= {
			'parameters': {
				'action':	'togglevisibility',
				'asset':	idAsset
			}
		};

		Todoyu.send(url, options);
		$('asset-' + idAsset + '-icon-public').toggleClassName('not');
	},



	/**
	 * Update assets tab of given task
	 * 
	 * @param	{Integer}		idTask
	 */
	updateTab: function(idTask) {
		var url		= Todoyu.getUrl('assets', 'tasktab');
		var options	= {
			'parameters': {
				'action':	'tab',
				'task':		idTask
			}
		};
		var target	= 'task-' + idTask + '-tabcontent-assets';
		
		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Set label of task assets tab
	 * 
	 * @param	{Integer}	idTask
	 * @param	{String}	label
	 */
	setTabLabel: function(idTask, label) {
		$('task-' + idTask + '-tab-assets-label').select('.labeltext').first().update(label);
	},



	/**
	 * Toggle assets list visibility
	 * 
	 * @param	{Integer}		idTask
	 */
	toggleList: function(idTask) {
		this.List.toggle(idTask);
	}

};