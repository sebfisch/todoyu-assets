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
 *	Assets handling inside of task form
 *
 * @class		assets
 * @namespace	Todoyu.Ext.assets.TaskEdit
 */
Todoyu.Ext.assets.TaskEdit = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.assets,



	/**
	 * Show (evoke adding of) upload form and hide the button making it shown
	 *
	 * @method	showUploadForm
	 * @param	{Element}	form
	 */
	showUploadForm: function(form) {
		var idTask	= form.id.split('-')[1];

		this.addUploadForm(idTask);
		$$('button.buttonUploadAsset')[0].hide();
	},



	/**
	 * Remove upload form and unhide button making it shown
	 *
	 * @method	removeUploadForm
	 */
	removeUploadForm: function() {
		if( Object.isElement( $('assets-uploadform') ) ) {
			$('assets-uploadform').remove();
		}
		$$('button.buttonUploadAsset').first().show();
	},



	/**
	 * Add asset file upload form
	 *
	 * @method	addUploadForm
	 * @param	{Number}	idTask
	 */
	addUploadForm: function(idTask) {
		idTask	=	idTask ? idTask : 0;

		var url		= Todoyu.getUrl('assets', 'taskEdit');
		var options	= {
			parameters: {
				'task':		idTask,
				action:	'assetsuploadform'
			}
		};
		var target	= $$('button.buttonUploadAsset')[0].id;
		Todoyu.Ui.append(target, url, options);
	},



	/**
	 * Upload asset file
	 *
	 * @method	upload
	 * @param	{Element}	form
	 */
	upload: function(form) {
		var field	= $(form).down('input[type=file]');

		if( field.value !== '' ) {
				// Create iFrame for asset file upload
			Todoyu.Form.addIFrame('assetfile');

			$(form).submit();
		}
	},



	/**
	 * Asset upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{Number}	idTask
	 */
	uploadFinished: function(idTask) {
		this.removeUploadForm();
		this.refreshFileOptions(idTask);

			// Update assets list and tab of task
		if( idTask > 0 ) {
			if( Todoyu.exists('task-' + idTask + '-assets-commands') ) {
				Todoyu.Ext.assets.List.refresh(idTask);
			} else {
				Todoyu.Ext.assets.updateTab(idTask);
			}
		}

		Todoyu.notifySuccess('[LLL:core.file.upload.succeeded]');
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 *
	 * @method	uploadFailed
	 * @param	{Number}		error		1 = file size exceeded, 2 = failure
	 * @param	{String}		filename
	 * @param	{Number}		maxFileSize
	 * @param	{Number}		idTask
	 */
	uploadFailed: function(error, filename, maxFileSize, idTask) {
		this.removeUploadForm();

		var info	= {
			'filename': 	filename,
			'maxFileSize':	maxFileSize,
			'id_task':		idTask
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:core.file.upload.failed.maxFileSizeExceeded]';
		} else {
			msg	= '[LLL:core.file.upload.error.uploadfailed]';
		}

		Todoyu.notifyError(msg.interpolate(info));
	},



	/**
	 * Handle change of selection in asset file selector: hide / show deletion option
	 *
	 * @method	onFileSelectionChange
	 * @param	{Element}	selectField
	 */
	onFileSelectionChange: function(selectField) {
		var idTask	= selectField.id.split('-')[1];

		this.toggleOptions(idTask);
	},



	/**
	 * Toggle asset options (file selector, delete option) visibility
	 *
	 * @method	toggleOptions
	 * @param	{Number}	idTask
	 */
	toggleOptions: function(idTask) {
		idTask		= idTask ? parseInt(idTask, 10) : 0;

		var amountAssets			= this.getAmountAssetFiles();
		var selectedAssetFilename	= this.getSelectedAssetFilename();
		var assetSelector			= this.getAssetSelector();

		var action	= amountAssets == 0 || selectedAssetFilename == '' ? 'addClassName' : 'removeClassName';

			// Toggle asset file selector visibility
		assetSelector[action]('displayNone');
			// Toggle delete button visibility
		assetSelector.up('fieldset').down('div.fieldnameDelete')[action]('displayNone');
	},






	/**
	 * Delete selected temporary asset file from server
	 *
	 * @method	removeSelectedTempAsset
	 * @param	{Number}	idTask
	 */
	removeSelectedTempAsset: function(idTask) {
		var idAssetRecord	= this.getSelectedAssetFileID(idTask);
		var filename 		= this.getSelectedAssetFilename(idTask);

		if( filename && confirm('[LLL:core.file.confirm.delete]' + ' ' + filename) ) {
			var url		= Todoyu.getUrl('assets', 'taskEdit');
			var options	= {
				parameters: {
					action:		'deletetempassetfile',
					'record':		idAssetRecord,
					'filename':		filename
				},
				onComplete: this.onRemovedAsset.bind(this, filename, idTask)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Cleanup: remove all temporary uploaded asset files
	 *
	 * @method	removeAllTempAssets
	 */
	removeAllTempAssets: function() {
		var url		= Todoyu.getUrl('assets', 'taskEdit');
		var options	= {
			parameters: {
				action:		'deletealltempassetfiles'
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Check whether task is being created via quicktask form
	 *
	 * @method	isQuicktask
	 * @return	{Boolean}
	 */
	isQuicktask: function() {
		return Todoyu.exists('quicktask');
	},



	/**
	 * Check whether quick create popup is open
	 *
	 * @return	{Boolean}
	 */
	isQuickCreate: function() {
		return Todoyu.exists('quickcreate');
	},



	/**
	 * Get element ID of asset files selector in current task form
	 *
	 * @method	getAssetSelectorID
	 * @param	{Number}	idTask
	 * @param	{Boolean}	isQuicktask
	 */
	getAssetSelectorID: function(idTask, isQuicktask) {
		isQuicktask	= isQuicktask ? isQuicktask : this.isQuicktask(idTask);

		return (isQuicktask ? 'quicktask-' : 'task-') + idTask + '-field-id-asset';
	},



	/**
	 * Get asset selector element
	 *
	 * @return	{Element}
	 */
	getAssetSelector: function() {
		var container;
		if( this.isQuicktask() ) {
			container	= $('quicktask');
		} else if( this.isQuickCreate() ) {
			container	= $('quickcreate');
		} else {
			container	= $('content');
		}

		return container.down('div.fieldnameIdasset select');
	},



	/**
	 * Get amount of asset file options
	 *
	 * @method	getAmountAssetFiles
	 * @return	{Number}
	 */
	getAmountAssetFiles: function() {
		return this.getAssetSelector().options.length;
	},



	/**
	 * Get ID of selected asset file (option value)
	 *
	 * @method	getSelectedAssetFileID
	 * @param	{Number}	idTask
	 * @param	{Boolean}	isQuicktask
	 * @return	{String}
	 */
	getSelectedAssetFileID: function(idTask, isQuicktask) {
		isQuicktask	= isQuicktask ? isQuicktask : this.isQuicktask(idTask);

		return $F(this.getAssetSelectorID(idTask, isQuicktask));
	},



	/**
	 * Get selected template file (option label)
	 *
	 * @method	getSelectedAssetFilename
	 * @return	{String}
	 */
	getSelectedAssetFilename: function() {
		var selectElement		= this.getAssetSelector();

		if( selectElement ) {
			return $F(selectElement);
		} else {
			return '';
		}
	},



	/**
	 * Evoked after completion of removal of asset file
	 *
	 * @method	onRemovedAsset
	 * @param	{String}			filename
	 * @param	{Number}			idTask
	 * @param	{Ajax.Response}		response
	 */
	onRemovedAsset: function(filename, idTask, response) {
		if( response.getTodoyuHeader('success') == 1 ) {
			this.refreshFileOptions(idTask);
			Todoyu.notifySuccess('[LLL:core.file.notify.delete.success]' + ' ' + filename);
		} else {
			Todoyu.notifyError('[LLL:core.file.notify.delete.error]' + ' ' + filename);
		}
	},



	/**
	 * Refresh assets file selector options
	 *
	 * @method	refreshFileOptions
	 * @param	{Number}	idTask
	 */
	refreshFileOptions: function(idTask) {
		var url		= Todoyu.getUrl('assets', 'taskEdit');
		var options	= {
			parameters: {
				'id_task':	idTask,
				action:	'assetfileoptions'
			},
			onComplete: this.onRefreshedFileOptions.bind(this, idTask)
		};

		var targetID	= this.getAssetSelectorID(idTask);

		Todoyu.Ui.update(targetID, url, options);
	},



	/**
	 * Evoked upon completion of refresh of template file selector options: refresh file option buttons
	 *
	 * @method	onRefreshedFileOptions
	 * @param	{Number}	idTask
	 * @param	{Event}		event
	 */
	onRefreshedFileOptions: function(idTask, event) {
		this.toggleOptions(idTask);
	},



	/**
	 * Init asset file operation buttons (upload, delete)
	 *
	 * @method	initFileOperationButtons
	 * @param	{Number}	idTask
	 */
	initFileOperationButtons: function(idTask) {
		this.toggleOptions(idTask);
	},



	/**
	 * Hooked handler when task edit or create is being cancelled: remove temporary uploaded assets
	 *
	 * @method	onCancelledTaskEdit
	 * @hook	project.task.edit.cancelled
	 */
	onCancelledTaskEdit: function(idTask) {
		this.removeAllTempAssets();
	},



	/**
	 * Hooked handler when quicktask popup is being closed: remove temporary uploaded assets
	 *
	 * @method	onCloseQuicktaskPopup
	 * @hook	project.quickTask.closePopup
	 */
	onCloseQuicktaskPopup: function() {
		this.removeAllTempAssets();
	}

};