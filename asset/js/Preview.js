/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Assets preview
 *
 * @class		Preview
 * @namespace	Todoyu.Ext.assets
 */
Todoyu.Ext.assets.Preview = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.assets,

	/**
	 * Flag whether preview is currently being loaded
	 *
	 * @property	hidden
	 * @type		Boolean
	 */
	loading:	false,

	/**
	 * Trigger to deactivate preview
	 *
	 * @property	disabled
	 * @type		Boolean
	 */
	disabled:	false,



	/**
	 * Enable previewing
	 *
	 * @method	enable
	 */
	enable: function() {
		this.disabled	= false;
	},



	/**
	 * Disable previewing
	 *
	 * @method	disable
	 */
	disable: function() {
		this.hide(true);
		this.disabled	= true;
	},



	/**
	 * Un-hide preview of given asset, load prior if not in DOM yet
	 *
	 * @method	show
	 * @param	{Number}	idAsset
	 */
	show: function(idAsset) {
		this.hidden	= false;

		if( this.loading === true ) {
			return false;
		}

		this.loading = true;

		var preview = $('asset-preview-'  + idAsset);
		if( preview ) {
			this.loading = false;
			preview.show();
		} else {
				// Have it loaded and shown
			this.loadPreview(idAsset);
		}
	},



	/**
	 * Hide the given asset's preview
	 *
	 * @method	hide
	 * @param	{Number}	idAsset
	 */
	hide: function(idAsset) {
		var preview = $('asset-preview-' + idAsset);

		if( preview ) {
			preview.hide();
		}
	},



	/**
	 * Loading given asset's preview
	 *
	 * @method	loadPreview
	 * @param	{Number}	idAsset
	 */
	loadPreview: function(idAsset) {
		var url		= Todoyu.getUrl('assets', 'preview');
		var options	= {
			parameters: {
				action:		'get',
				'asset':	idAsset
			},
			onComplete: this.onPreviewLoaded.bind(this, idAsset)
		};

		this.loading = true;

		Todoyu.send(url, options);
	},



	/**
	 * Show preview after being loaded via AJAX.
	 *
	 * @method	onPreviewLoaded
	 * @param	{Number}			idAsset
	 * @param	{Ajax.Response}		response		Ajax response
	 */
	onPreviewLoaded: function(idAsset, response) {
		var row = $('task-asset-' + idAsset);
		row.insert({
			after: response.responseText
		});

		this.loading= false;

		if( !this.hidden ) {
			this.show(idAsset);
		}
	}

};