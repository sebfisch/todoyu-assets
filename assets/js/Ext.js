/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 *	Ext: assets
 */

Todoyu.Ext.assets = {

	PanelWidget: {},

	Headlet: {},



	/**
	 *	Download asset
	 *
	 *	@param	Integer	idAsset
	 */
	download: function(idAsset) {
		var params	= {
			'action': 'download',
			'asset': idAsset
		};

		Todoyu.goTo('assets', 'asset', params);
	},




	/**
	 *	Download (zipped) selection of assets of given task
	 *
	 *	@param	Integer	idTask
	 */
	downloadSelection: function(idTask) {
		var selectedAssets = this.List.getSelectedAssets(idTask);
				
		if( selectedAssets.size() === 0 ) {
			Todoyu.notifyError('Please select at least one file', 3);
		} else if( selectedAssets.size() === 1 ) {
			Todoyu.notifyInfo('You only selected one file, normal file download', 3);
			this.download(selectedAssets.first());
		} else {
			Todoyu.notifyInfo('The selected files have been packed into an archive for download', 3);
			var params = {
				'action': 'download',
				'task': idTask,
				'assets': selectedAssets.join(',')
			};
						
			Todoyu.goTo('assets', 'zip', params);
		}
	},



	/**
	 *	Remove given asset
	 *
	 *	@param	Integer	idAsset
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
			Effect.Fade('asset-' + idAsset);
		}
	},



	/**
	 * Handler to be called after having deleted a file: updates file list
	 * 
	 * @param	Interger	idAsset
	 * @param	Object		response 
	 */
	onRemoved: function(idAsset, response) {
		var idTask	= response.getTodoyuHeader('idTask');
		var label	= response.getTodoyuHeader('tabLabel');
		
		this.setTabLabel(idTask, label);
		this.updateTab(idTask);
	},



	/**
	 *	Toggle given asset visibility (hide from customers?)
	 *
	 *	@param	Integer	idAsset
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
	 * @param	Integer		idTask
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
	 * @param	Integer	idTask
	 * @param	String	label
	 */	
	setTabLabel: function(idTask, label) {
		$('task-' + idTask + '-tab-assets-label').select('.labeltext').first().update(label);
	},



/* -----------------------------------------------
	Todoyu.Ext.assets.List
-------------------------------------------------- */

	/**
	 *	List assets
	 */
	List: {
		
		/**
		 *	Refresh
		 *
		 *	@param	Integer	idTask
		 */
		refresh: function(idTask) {
			var list	= 'task-' + idTask + '-assets-list';
			var url		= Todoyu.getUrl('assets', 'tasktab');
			var options	= {
				'parameters': {
					'action':	'list',
					'task':		idTask
				}
			};

			if( Todoyu.exists(list) ) {
				Todoyu.Ui.replace(list, url, options);
			} else {
				var target	= 'task-' + idTask + '-tabcontent-assets';
				Todoyu.Ui.update(target, url, options);
			}
		},


		/**
		 *	Hover given asset
		 *
		 *	@param	Integer	idAsset
		 */
		hover: function(idAsset) {
			$('asset-' + idAsset).toggleClassName('hover');
		},



		/**
		 *	Select asset
		 *
		 *	@param	Integer	idAsset
		 */
		select: function(idAsset) {
			if( $('asset-' + idAsset + '-checkbox').checked ) {
				this.unCheck(idAsset);
			} else {
				this.check(idAsset);
			}
		},


		/**
		 *	Set given asset checked
		 *
		 *	@param	Integer	idAsset
		 */
		check: function(idAsset) {
			$('asset-' + idAsset).addClassName('selected');
			$('asset-' + idAsset + '-checkbox').checked = true;
		},


		/**
		 *	Set asset unchecked
		 *
		 *	@param	Integer	idAsset
		 */
		unCheck: function(idAsset) {
			$('asset-' + idAsset).removeClassName('selected');
			$('asset-' + idAsset + '-checkbox').checked = false;
		},



		/**
		 *	Select all assets of given task
		 *
		 *	@param	Integer	idTask
		 */
		selectAll: function(idTask) {
			var list 	= $('task-' + idTask + '-assets-tablebody');
			var boxes	= list.select('input');

				// All already checked?
			var notAll = false;
			boxes.each(function(item){
				if( ! item.checked ) {
					notAll = true;
					return;
				}
			});

			var checkAll = notAll ? true : false ;

			boxes.each(function(item){
				if( checkAll === true ) {
					this.check(item.value);
				} else {
					this.unCheck(item.value);
				}
			}.bind(this));

			$('task-' + idTask + '-assets-checkallbox').checked = checkAll;
		},



		/**
		 *	Get selected assets of given task
		 *
		 *	@param	Integer	idTask
		 *	@return	Array
		 */
		getSelectedAssets: function(idTask) {
			var list 	= $('task-' + idTask + '-assets-tablebody');
			var boxes	= list.select('input');
			var assetIDs= [];

			boxes.each(function(item){
				if( item.checked ) {
					assetIDs.push(item.value);
				}
			});

			return assetIDs;
		}
	},



/* -----------------------------------------------
	Todoyu.Ext.assets.Upload
-------------------------------------------------- */

	/**
	 *	Asset upload methods
	 */
	Upload: {

		/**
		 *	Show asset upload form of given task
		 *
		 *	@param	Integer	idTask
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
		 *	onChange handler of assets upload form to given task
		 *
		 *	@param	Integer	idTask
		 */
		onChange: function(idTask) {
			this.createIFrame(idTask);
			this.showUploader(idTask, this.getField(idTask).value);
			this.submit(idTask);
			this.replaceField(idTask);
		},


		/**
		 *	Assets upload form submission handler
		 *
		 *	@param	Integer	idTask
		 */
		submit: function(idTask) {
			var form = this.getForm(idTask);

			form.writeAttribute('target', 'asset-upload-iframe-' + idTask);
			form.submit();
		},



		/**
		 *	Get asset upload form file field's value of given task
		 *
		 *	@param	Integer	idTask
		 *	@return	Element
		 */
		getField: function(idTask) {
			return $('asset-' + idTask + '-field-file');
		},



		/**
		 *	Get assets upload form
		 *
		 *	@param	Integer	idTask
		 *	@return	Element
		 */
		getForm: function(idTask) {
			return $('asset-' + idTask + '-form');
		},



		/**
		 *	Create iFrame for assets upload
		 *
		 *	@param	Integer	idTask
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
		 *	Replace field inside assets upload form
		 *
		 *	@param	Integer	idTask
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
		 *	Show assets uploader
		 *
		 *	@param	Integer	idTask
		 *	@param	String	filename
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
		 *	Asset upload finished handler
		 *
		 *	@param	Integer	idTask
		 *	@param	String	filename
		 */
		uploadFinished: function(idTask, tabLabel) {
				// Remove uploader progress bar
			$('asset-uploader-element').remove();
			
			if( Todoyu.exists('task-' + idTask + '-assets-commands') ) {
				Todoyu.Ext.assets.List.refresh(idTask);
			} else {
				Todoyu.Ext.assets.updateTab(idTask);
			}

			Todoyu.Ext.assets.setTabLabel(idTask, tabLabel);			
		}
	},

	/**
	 * Toggle assets list visibility
	 */
	toggleList: function(idTask) {
		Todoyu.Ui.toggle('task-' + idTask + '-assets-list');
	}

};