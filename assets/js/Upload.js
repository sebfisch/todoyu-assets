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
 * Asset upload methods
 */
Todoyu.Ext.assets.Upload = {

	ext: Todoyu.Ext.assets,



	 /**
	 * Show asset upload form of given task
	 *
	 * @param	Integer	idTask
	 */
	showForm: function(idTask) {
		var form	= 'task-' + idTask + '-assetform';

		if( ! Todoyu.exists(form) ) {
			var url		= Todoyu.getUrl('assets', 'tasktab');
			var options	= {
				'parameters': {
					'action':	'uploadform',
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
	 * @param	Integer	idTask
	 */
	onChange: function(idTask) {
		this.createIFrame(idTask);
		this.showUploader(idTask, this.getField(idTask).value);
		this.submit(idTask);
		this.replaceField(idTask);
	},



	/**
	 * Assets upload form submission handler
	 *
	 * @param	Integer	idTask
	 */
	submit: function(idTask) {
		var form = this.getForm(idTask);

		form.writeAttribute('target', 'asset-upload-iframe-' + idTask);
		form.submit();
	},



	/**
	 * Get asset upload form file field's value of given task
	 *
	 * @param	Integer	idTask
	 * @return	Element
	 */
	getField: function(idTask) {
		return $('asset-' + idTask + '-field-file');
	},



	/**
	 * Get assets upload form
	 *
	 * @param	Integer	idTask
	 * @return	Element
	 */
	getForm: function(idTask) {
		return $('asset-' + idTask + '-form');
	},



	/**
	 * Create iFrame for assets upload
	 *
	 * @param	Integer	idTask
	 */
	createIFrame: function(idTask) {
		var idIframe= 'asset-upload-iframe-' + idTask;

		if( ! Todoyu.exists(idIframe) ) {
			var iframe	= new Element('iframe', {
				'name':		'asset-upload-iframe-' + idTask,
				'id':		'asset-upload-iframe-' + idTask,
				'class':	'asset-upload-iframe'
			});

			iframe.hide();

			$('task-' + idTask + '-assetform').insert(iframe);
		}
	},



	/**
	 * Replace field inside assets upload form
	 *
	 * @param	Integer		idTask
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
	 * @param	Integer		idTask
	 * @param	String		filename
	 */
	showUploader: function(idTask, filename) {
		var formElement = new Element('div', {
			'id': 'asset-uploader-element',
			'class': 'formElement'
		});
		var loaderText = new Element('div').update('[LLL:assets.upload.filename]' + ': ' + filename);
		var loaderImage = new Element('img', {
			'src': 'ext/assets/assets/img/uploader.gif'
		});

		formElement.insert(loaderText);
		formElement.insert(loaderImage);

		$('formElement-asset-' + idTask + '-field-file').insert({'before': formElement});
	},



	/**
	 * Remove uploader progress bar from DOM
	 */
	removeUploader: function() {
		$('asset-uploader-element').remove();
	},



	/**
	 * Asset upload finished handler
	 *
	 * @param	Integer		idTask
	 * @param	String		filename
	 */
	uploadFinished: function(idTask, tabLabel) {
		this.removeUploader();
		
		if( Todoyu.exists('task-' + idTask + '-assets-commands') ) {
			Todoyu.Ext.assets.List.refresh(idTask);
		} else {
			Todoyu.Ext.assets.updateTab(idTask);
		}

		Todoyu.notifySuccess('[LLL:assets.uploadOk]');

		Todoyu.Ext.assets.setTabLabel(idTask, tabLabel);
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 * 
	 * @param	Integer		error		1 = filesize exceeded, 2 = failure
	 * @param	String		filename
	 * @param	Integer		maxFileSize
	 */
	uploadFailed: function(error, filename, maxFileSize) {
		this.removeUploader();
		var info	= {
			'filename': 	filename,
			'maxFileSize':	maxFileSize
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:assets.maxFileSizeExceeded]';
		} else {
			msg	= '[LLL:assets.uploadFailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 10);
	}

};