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
 * Asset upload methods
 *
 * @class		Upload
 * @namespace	Todoyu.Ext.assets
 */
Todoyu.Ext.assets.Upload = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.assets,

	/**
	 * Upload activity flag
	 *
	 * @property	active
	 * @type		Boolean
	 */
	active: false,



	/**
	 * onChange handler of assets upload form to given task
	 *
	 * @method	onChange
	 * @param	{Number}	idTask
	 */
	onChange: function(idTask) {
		this.showProgressBar(idTask, this.getField(idTask).value);
		this.submit(idTask);
	},



	/**
	 * Assets upload form submission handler
	 *
	 * @method	submit
	 * @param	{Number}	idTask
	 */
	submit: function(idTask) {
		this.active	= true;
		var form	= this.getForm(idTask);
		var iFrame	= Todoyu.Form.submitFileUploadForm(form);

			// Register callback to check after 20 seconds if upload failed
		this.uploadFailingDetection.bind(this, idTask, iFrame).delay(20);
	},



	/**
	 * Check if upload iframe has loaded, but not set upload flag
	 * This means an error page has been loaded and the upload failed
	 *
	 * @method	uploadFailingDetection
	 * @param	{Number}	idTask
	 * @param	{Element}	iFrame
	 */
	uploadFailingDetection: function(idTask, iFrame) {
		if( this.active === true && iFrame.contentDocument.URL !== 'about:blank' ) {
			this.uploadFailed(idTask, 0, '');
		}
	},



	/**
	 * Get asset upload form file field's value of given task
	 *
	 * @method	getField
	 * @param	{Number}	idTask
	 * @return	{Element}
	 */
	getField: function(idTask) {
		return $('task-' + idTask + '-asset-file');
	},



	/**
	 * Get assets upload form
	 *
	 * @method	getForm
	 * @param	{Number}	idTask
	 * @return	{Element}
	 */
	getForm: function(idTask) {
		return $('task-' + idTask + '-asset-form');
	},



	/**
	 * Show assets uploader
	 *
	 * @method	showProgressBar
	 * @param	{Number}	idTask
	 * @param	{Boolean}	show
	 */
	showProgressBar: function(idTask, show) {
		$('task-' + idTask + '-asset-progress')[show?'show':'hide']();
	},



	/**
	 * Asset upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{Number}		idTask
	 * @param	{String}		tabLabel
	 */
	uploadFinished: function(idTask, tabLabel) {
		this.active = false;

		this.showProgressBar(idTask, false);

		Todoyu.Ext.project.Task.refreshHeader(idTask);

		if( Todoyu.exists('task-' + idTask + '-assets-commands') ) {
			Todoyu.Ext.assets.List.refresh(idTask);
		} else {
			Todoyu.Ext.assets.updateTab(idTask);
		}

		Todoyu.notifySuccess('[LLL:assets.ext.uploadOk]');

		Todoyu.Ext.assets.setTabLabel(idTask, tabLabel);
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 *
	 * @method	uploadFailed
	 * @param	{Number}		idTask
	 * @param	{Number}		error		1 = file size exceeded, 2 = failure
	 * @param	{String}		filename
	 * @param	{Number}		maxFileSize
	 */
	uploadFailed: function(idTask, error, filename, maxFileSize, maxLengthFileName) {
		this.active = false;

		this.showProgressBar(idTask, false);

		var info	= {
			filename:			filename,
			maxFileSize:		maxFileSize,
			maxLengthFileName:	maxLengthFileName
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:assets.ext.maxFileSizeExceeded]';
		} else if( error === 3 ) {
			msg = '[LLL:assets.ext.maxLengthFileNameExceeded]';
		} else {
			msg	= '[LLL:assets.ext.uploadFailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 'fileupload');
	}

};