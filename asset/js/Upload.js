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
	 * Show asset upload form of given task
	 *
	 * @method	showForm
	 * @param	{Number}	idTask
	 */
	showForm: function(idTask) {
		if( this.active === true ) {
			return;
		}

		var form	= 'task-' + idTask + '-assetform';

		if( ! Todoyu.exists(form) ) {
			var url		= Todoyu.getUrl('assets', 'tasktab');
			var options	= {
				parameters: {
					action:	'uploadform',
					'task':		idTask
				}
			};
			var target	= 'task-' + idTask + '-assets-commands';

			Todoyu.Ui.append(target, url, options);
		}
	},



	/**
	 * onChange handler of assets upload form to given task
	 *
	 * @method	onChange
	 * @param	{Number}	idTask
	 */
	onChange: function(idTask) {
		this.addIFrame(idTask);
		this.showProgressBar(idTask, this.getField(idTask).value);
		this.hideUploadField(idTask);
		this.submit(idTask);
		this.replaceField(idTask);
	},



	/**
	 * Assets upload form submission handler
	 *
	 * @method	submit
	 * @param	{Number}	idTask
	 */
	submit: function(idTask) {
		this.active	= true;

		this.getForm(idTask).writeAttribute('target', 'upload-iframe-asset-' + idTask);
		this.getForm(idTask).submit();

			// Register callback to check after 20 seconds if upload failed
		this.uploadFailingDetection.bind(this, idTask).delay(20);
	},



	/**
	 * Check if upload iframe has loaded, but not set upload flag
	 * This means an error page has been loaded and the upload failed
	 *
	 * @method	uploadFailingDetection
	 * @param	{Number}	idTask
	 */
	uploadFailingDetection: function(idTask) {
		var iframe = Todoyu.Form.getIFrame('asset-' + idTask);

		if( this.active === true && iframe.contentDocument.URL !== 'about:blank' ) {
			this.uploadFailed(0, '');
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
		return $('asset-' + idTask + '-field-file');
	},



	/**
	 * Get assets upload form
	 *
	 * @method	getForm
	 * @param	{Number}	idTask
	 * @return	{Element}
	 */
	getForm: function(idTask) {
		return $('asset-' + idTask + '-form');
	},



	/**
	 * Create iFrame for assets upload
	 *
	 * @method	addIFrame
	 * @param	{Number}	idTask
	 */
	addIFrame: function(idTask) {
		Todoyu.Form.addIFrame('asset-' + idTask);
	},



	/**
	 * Remove the upload iframe
	 *
	 * @method	removeIFrame
	 */
	removeIFrame: function() {
		$$('iframe.uploadIframe').first().remove();
	},



	/**
	 * Replace field inside assets upload form
	 *
	 * @method	replaceField
	 * @param	{Number}		idTask
	 */
	replaceField: function(idTask) {
		var old		= this.getField(idTask);
		var field	= new Element('input', {
			'id':		old.readAttribute('id'),
			'type':		old.readAttribute('type'),
			'onchange':	old.readAttribute('onchange'),
			'name':		old.readAttribute('name')
		});

		old.replace(field);
	},



	/**
	 * Show assets uploader
	 *
	 * @method	showProgressBar
	 * @param	{Number}		idTask
	 * @param	{String}		filename
	 */
	showProgressBar: function(idTask, filename) {
		var formElement = new Element('div', {
			'id':		'asset-uploader-element',
			'class':	'formElement'
		});
		var loaderText = new Element('div').update('[LLL:assets.ext.upload.filename]' + ': ' + filename);
		var loaderImage = new Element('img', {
			'src': 'core/asset/img/progress.gif'
		});

		formElement.insert(loaderText);
		formElement.insert(loaderImage);

		$('formElement-asset-' + idTask + '-field-file').insert({
			before: formElement
		});
	},



	/**
	 * Remove uploader progress bar from DOM
	 *
	 * @method	removeProgressBar
	 */
	removeProgressBar: function() {
		$('asset-uploader-element').remove();
	},



	/**
	 * Hide upload field to prevent multiple uploads at the same time
	 *
	 * @method	hideUploadField
	 * @param	{Number}		idTask
	 */
	hideUploadField: function(idTask) {
		$('formElement-asset-' + idTask + '-field-file').hide();
	},



	/**
	 * Show upload field which was hidden during the upload process
	 *
	 * @method	showUploadField
	 */
	showUploadField: function() {
		var fields	= $$('input[type=file][id^=asset-][id$=-field-file]');

		fields.each(function(element){
			var formElement	= element.up('div.typeUpload');

				// If is in a form element
			if( formElement ) {
					// If form element is in an asset form and is hidden
				if( formElement.up('form.formAsset') && !formElement.visible() ) {
					formElement.show();
					return;
				}
			}
		});
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

		this.removeProgressBar();
		this.removeIFrame();
		this.showUploadField();

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
	 * @param	{Number}		error		1 = filesize exceeded, 2 = failure
	 * @param	{String}		filename
	 * @param	{Number}		maxFileSize
	 */
	uploadFailed: function(error, filename, maxFileSize, maxLengthFileName) {
		this.active = false;

		this.removeProgressBar();
		this.removeIFrame();
		this.showUploadField();

		var info	= {
			'filename': 	filename,
			'maxFileSize':	maxFileSize,
			'maxLengthFileName': maxLengthFileName
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:assets.ext.maxFileSizeExceeded]';
		} else if( error === 3 ) {
			msg = '[LLL:assets.ext.maxLengthFileNameExceeded]';
		} else {
			msg	= '[LLL:assets.ext.uploadFailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 10);
	}

};