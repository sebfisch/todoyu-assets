<?php
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
 * Renderer for assets inside task edit form
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskEditRenderer {

	/**
	 * Render invoice templates upload form
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderAssetUploadForm($idTask) {
		$idTask	= intval($idTask);

			// Construct form object
		$xmlPath	= 'ext/assets/config/form/taskedit-upload.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);

			// Set form data
		$formData	= array(
			'id_task'		=> $idTask,
			'MAX_FILE_SIZE'	=> intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size'])
		);
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData);

		$form->setFormData($formData);
		$form->setUseRecordID(false);

			// Render form
		$tmpl	= 'ext/assets/view/taskedit-uploadform.tmpl';
		$data	= array(
			'formhtml'	=> $form->render()
		);

			// Render form wrapped via dwoo template
		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render asset upload iFrame content
	 *
	 * @param	String		$fileName
	 * @return	String
	 */
	public static function renderUploadframeContent($fileName, $idTask) {
		$idTask	= intval($idTask);

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.TaskEdit.uploadFinished(' . $idTask . ');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content of asset uploader iFrame when upload failed
	 *
	 * @param	String		$error
	 * @param	String		$fileName
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderUploadframeContentFailed($error, $fileName, $idTask) {
		$idTask	= intval($idTask);

		$maxFileSize	= intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']);

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.TaskEdit.uploadFailed(' . $error . ', \'' . $fileName . '\', ' . $maxFileSize . ', ' . $idTask . ');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render asset files select options
	 *
	 * @return	String
	 */
	public static function renderAssetFileOptions($idTask = 0) {
		$idTask	= intval($idTask);

		$options	= TodoyuAssetsAssetManager::getTaskAssetFileOptions($idTask);

		$tmpl	= 'core/view/form/FormElement_Select_Options.tmpl';
		$data	= array(
			'options'	=> $options,
			'value'		=> array()
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>