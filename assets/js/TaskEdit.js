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
			'parameters': {
				'task':		idTask,
				'action':	'assetsuploadform'
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

		Todoyu.notifyError(msg.interpolate(info), 10);
	},



	/**
	 * Handle change of selection in asset file selector: hide / show deletion option
	 *
	 * @method	onFileSelectionChange
	 * @param	{Element}	selectField
	 */
	onFileSelectionChange: function(selectField) {
		var idTask	= selectField.id.split('-')[1];

		this.toggleButtons(idTask);
	},



	/**
	 * Toggle asset buttons (for adding / removing template file) visibility
	 *
	 * @method	toggleButtons
	 * @param	{Number}	idTask
	 */
	toggleButtons: function(idTask) {
		idTask		= idTask ? parseInt(idTask, 10) : 0;

		var action;
		if( this.getAmountAssetFiles(idTask) == 0 || this.getSelectedAssetFilename(idTask) == '' ) {
			action	= 'add';
		} else {
			action	= 'remove';
		}

		$('content').down('div.fieldnameDelete')[action + 'ClassName']('displayNone');
	},



	/**
	 * Delete temporary asset file from server
	 *
	 * @method	removeTempAsset
	 * @param	{Integer}	idTask
	 */
	removeTempAsset: function(idTask) {
		var idAssetRecord	= this.getSelectedAssetFileID(idTask);
		var filename 		= this.getSelectedAssetFilename(idTask);

		if( confirm('[LLL:core.file.confirm.delete]' + ' ' + filename) ) {
			var url		= Todoyu.getUrl('assets', 'taskEdit');
			var options	= {
				'parameters': {
					'action':		'deletetempassetfile',
					'record':		idAssetRecord,
					'filename':		filename
				},
				'onComplete': this.onRemovedAsset.bind(this, filename, idTask)
			};

			Todoyu.send(url, options);
		}
	},


	/**
	 * Get amount of asset file options
	 *
	 * @method	getAmountAssetFiles
	 * @param	{Number}	idTask
	 * @return	{Number}
	 */
	getAmountAssetFiles: function(idTask) {
		return $('task-' + idTask + '-field-id-asset').options.length;
	},



	/**
	 * Get ID of selected asset file (option value)
	 *
	 * @method	getSelectedAssetFile
	 * @param	{Number}	idTask
	 */
	getSelectedAssetFileID: function(idTask) {
		return $F('task-' + idTask + '-field-id-asset');
	},



	/**
	 * Get selected template file (option label)
	 *
	 * @method	getSelectedAssetFilename
	 * @param	{Number}	idTask
	 * @return	{String}
	 */
	getSelectedAssetFilename: function(idTask) {
		var idAsset	= this.getSelectedAssetFileID(idTask);
		var option		= $('content').down('div.fieldnameIdasset select').down('[value=' + idAsset + ']');
		var label		= option.innerHTML;

			// Remove file size and date comment from option label
		return label.split(' (')[0];
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
			'parameters': {
				'id_task':	idTask,
				'action':	'assetfileoptions'
			},
			'onComplete': this.onRefreshedFileOptions.bind(this, idTask)
		};
		var target	= $('content').down('div.fieldnameIdasset select').id;

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Evoked upon completion of refresh of template file selector options: refresh file option buttons
	 *
	 * @method	onRefreshedFileOptions
	 * @param	{Number}	idTask
	 * @param	{Event}		event
	 */
	onRefreshedFileOptions: function(idTask, event) {
		this.toggleButtons(idTask);
	},



	/**
	 * Init asset file operation buttons (upload, delete)
	 *
	 * @method	initFileOperationButtons
	 * @param	{Number}	idTask
	 */
	initFileOperationButtons: function(idTask) {
		this.toggleButtons(idTask);
	}

};