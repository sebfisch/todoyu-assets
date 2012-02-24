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
	 * @param	{Element}	button
	 */
	showUploadForm: function(button) {
		var idTask = button.id.split('-')[1];

		this.addUploadForm(idTask);
		this.showButtons(idTask, false);
	},


	/**
	 * Cancel upload
	 *
	 * @param	{Number}	idTask
	 */
	cancelUpload: function(idTask) {
		this.removeUploadForm(idTask);
		this.showButtons(idTask, true);
	},



	/**
	 * Show/hide buttons
	 *
	 * @param	{Number}	idTask
	 * @param	{Boolean}	show
	 */
	showButtons: function(idTask, show) {
		$('task-' + idTask + '-fieldset-assets').select('.fElement.typeButton').invoke(show?'show':'hide');
	},



	/**
	 * Toggle all form elements depending on current state
	 * Elements: file list, delete button
	 *
	 * @param	{Number}	idTask
	 */
	toggleFormElements: function(idTask) {
		var hasFiles	= $('task-' + idTask + '-field-id-asset').select('option').size() > 0;

		$('formElement-task-' + idTask + '-field-id-asset')[hasFiles?'show':'hide']();
		$('formElement-task-' + idTask + '-field-delete')[hasFiles?'show':'hide']();
	},



	/**
	 * Remove upload form and unhide button making it shown
	 *
	 * @method	removeUploadForm
	 * @param	{Number}	idTask
	 */
	removeUploadForm: function(idTask) {
		if( $('task-' + idTask + '-assets-uploadform') ) {
			$('task-' + idTask + '-assets-uploadform').remove();
		}
	},



	/**
	 * Add asset file upload form
	 *
	 * @method	addUploadForm
	 * @param	{Number}	idTask
	 */
	addUploadForm: function(idTask) {
		var url		= Todoyu.getUrl('assets', 'taskEdit');
		var options	= {
			parameters: {
				action:	'uploadform',
				task:	idTask
			}
		};
		var target	= 'task-' + idTask + '-fieldset-assets';

		Todoyu.Ui.insert(target, url, options);
	},



	/**
	 * Upload asset file
	 *
	 * @method	upload
	 * @param	{Element}	field
	 */
	upload: function(field) {
		if( $F(field) !== '' ) {
				// Create iFrame for asset file upload
			Todoyu.Form.addIFrame('assetfile');

			$(field).form.submit();
		}
	},



	/**
	 * Asset upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{Number}	idTask
	 */
	uploadFinished: function(idTask) {
		this.removeUploadForm(idTask);
		this.showButtons(idTask, true);
		this.refreshFileOptions(idTask);

			// Update assets list and tab of task
		if( idTask > 0 && $('task-' + idTask + '-tabcontent-assets') ) {
			if( Todoyu.exists('task-' + idTask + '-assets-commands') ) {
				Todoyu.Ext.assets.List.refresh(idTask);
			} else {
				Todoyu.Ext.assets.updateTab(idTask);
			}
		}

		Todoyu.notifySuccess('[LLL:core.file.upload.succeeded]', 'fileupload');
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
		this.removeUploadForm(idTask);
		this.showButtons(idTask, true);
		this.toggleFormElements(idTask);

		var info	= {
			filename: 		filename,
			maxFileSize:	maxFileSize,
			id_task:		idTask
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:core.file.upload.failed.maxFileSizeExceeded]';
		} else {
			msg	= '[LLL:core.file.upload.error.uploadfailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 'fileupload');
	},



	/**
	 * Handle change of selection in asset file selector: hide / show deletion option
	 *
	 * @method	onFileSelectionChange
	 * @param	{Element}	selectField
	 */
	onFileSelectionChange: function(selectField) {
		var idTask	= selectField.id.split('-')[1];

	},




	/**
	 * Delete selected temporary asset file from server
	 *
	 * @method	removeSelectedTempAsset
	 * @param	{Number}	idTask
	 */
	removeSelectedTempAsset: function(idTask) {
		var fileKey		= this.getSelectedAssetFileID(idTask);
		var filename 	= this.getSelectedAssetFilename(idTask);

		if( confirm('[LLL:core.file.confirm.delete]' + ' ' + filename) ) {
			var url		= Todoyu.getUrl('assets', 'taskEdit');
			var options	= {
				parameters: {
					action:		'deletesessionfile',
					filekey:	fileKey,
					task:		idTask
				},
				onComplete: this.onRemovedAsset.bind(this, filename, idTask)
			};

			Todoyu.Ui.update(this.getAssetSelector(idTask), url, options);
		}
	},




	/**
	 * Cleanup: remove all temporary uploaded asset files
	 *
	 * @method	removeAllTempAssets
	 * @param	{Number}	idTask
	 */
	removeAllTempAssets: function(idTask) {
		var url		= Todoyu.getUrl('assets', 'taskEdit');
		var options	= {
			parameters: {
				action:	'deleteuploads',
				task:	idTask
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
	 * @method	getAssetSelector
	 * @return	{Element}
	 */
	getAssetSelector: function(idTask) {
		return $('task-' + idTask + '-field-id-asset');
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
	getSelectedAssetFilename: function(idTask) {
		var select		= this.getAssetSelector(idTask);

		return select.selectedIndex >= 0 ? select.options[select.selectedIndex].text : '';
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
		Todoyu.notifySuccess('[LLL:core.file.notify.delete.success]' + ' ' + filename, 'assets.taskedit.onremovedasset');

		this.toggleFormElements(idTask);
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
				action:	'sessionFiles',
				task:	idTask
			},
			onComplete: this.onRefreshedFileOptions.bind(this, idTask)
		};

		var target	= this.getAssetSelectorID(idTask);

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Evoked upon completion of refresh of template file selector options: refresh file option buttons
	 *
	 * @method	onRefreshedFileOptions
	 * @param	{Number}		idTask
	 * @param	{Ajax.Response}	response
	 */
	onRefreshedFileOptions: function(idTask, response) {
		this.toggleFormElements(idTask);
	},



	/**
	 * Hooked handler when task edit or create is being cancelled: remove temporary uploaded assets
	 *
	 * @method	onCancelledTaskEdit
	 * @hook	project.task.edit.cancelled
	 */
	onCancelledTaskEdit: function(idTask) {
		this.removeAllTempAssets(idTask);
	},



	/**
	 * Handle task edit form loading
	 *
	 * @param	{Number}	idTask
	 * @param	{Object}	options
	 */
	onTaskEditFormLoaded: function(idTask, options) {
		this.toggleFormElements(idTask);
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