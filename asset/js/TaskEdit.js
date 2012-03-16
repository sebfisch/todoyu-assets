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
	 * Toggle all form elements depending on current state
	 * Elements: file list, delete button
	 *
	 * @param	{Number}	idTask
	 */
	toggleFormElements: function(idTask) {
		var hasFiles= this.getAssetSelector(idTask).select('option').size() > 0;
		var method	= hasFiles ? 'show' : 'hide';

		$('formElement-task-' + idTask + '-field-assetlist')[method]();
		$('formElement-task-' + idTask + '-field-delete')[method]();
	},



	/**
	 * Upload asset file
	 *
	 * @method	uploadFileInline
	 * @param	{Element}	field
	 */
	uploadFileInline: function(field) {
		if( $F(field) !== '' ) {
			var url	= Todoyu.getUrl('assets', 'taskedit', {
				action: 'uploadassetfile'
			});

			Todoyu.Form.submitFileUploadForm(field.form, url);
		}
	},



	/**
	 * Asset upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{Number}	idTask
	 */
	uploadFinished: function(idTask) {
		this.toggleFormElements(idTask);
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
		this.toggleFormElements(idTask);

		var info	= {
			filename:		filename,
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
	 * Delete selected temporary asset file from server
	 *
	 * @method	removeSelectedTempAsset
	 * @param	{Number}	idTask
	 */
	removeSelectedTempAsset: function(idTask) {
		var fileKey		= this.getSelectedAssetFileID(idTask);
		var filename	= this.getSelectedAssetFilename(idTask);

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
	 * Get asset selector element
	 *
	 * @method	getAssetSelector
	 * @param	{Number}	idTask
	 * @return	{Element}
	 */
	getAssetSelector: function(idTask) {
		return $('task-' + idTask + '-field-assetlist');
	},



	/**
	 * Get ID of selected asset file (option value)
	 *
	 * @method	getSelectedAssetFileID
	 * @param	{Number}	idTask
	 * @return	{String}
	 */
	getSelectedAssetFileID: function(idTask) {
		return $F(this.getAssetSelector(idTask));
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

		var target	= this.getAssetSelector(idTask);

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
	}

};