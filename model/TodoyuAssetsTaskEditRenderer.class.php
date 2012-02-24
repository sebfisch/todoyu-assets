<?php
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
 * Renderer for assets inside task edit form
 *
 * @package		Todoyu
 * @subpackage	Assets
 */
class TodoyuAssetsTaskEditRenderer {

	/**
	 * Render upload form for assets
	 *
	 * @param	Integer		$idTask
	 * @return	String
	 */
	public static function renderAssetUploadForm($idTask) {
			// Construct form object
		$xmlPath	= 'ext/assets/config/form/task-inline-upload.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idTask);

			// Set form data
		$formData	= array(
			'id_task'		=> $idTask,
			'MAX_FILE_SIZE'	=> intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size'])
		);
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData);

		$form->setFormData($formData);

			// Render form
		$tmpl	= 'ext/assets/view/taskedit-uploadform.tmpl';
		$data	= array(
			'idTask'	=> $idTask,
			'formhtml'	=> $form->render()
		);

			// Render form wrapped via dwoo template
		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render asset upload iFrame content
	 *
	 * @param	String		$fileName
	 * @param	Integer		$idTask
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

		$maxFileSize	= TodoyuString::formatSize(intval(Todoyu::$CONFIG['EXT']['assets']['max_file_size']));

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.assets.TaskEdit.uploadFailed(' . $error . ', \'' . $fileName . '\', \'' . $maxFileSize . '\', ' . $idTask . ');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render options for files uploaded during task creation
	 *
	 * @param	Integer		$idTask
	 * @return	String		Rendered <option> elements
	 */
	public static function renderSessionFileOptions($idTask) {
		$uploader	= new TodoyuAssetsTempUploaderTask($idTask);
		$fileInfos	= $uploader->getFilesInfos();
		$options	= array();
		$fileInfos	= TodoyuArray::sortByLabel($fileInfos, 'time', true);

		foreach($fileInfos as $file) {
			$options[] = array(
				'value'	=> $file['key'],
				'label'	=> $file['name'] . ' (' . TodoyuTime::format($file['time'], 'timesec') . ', ' . TodoyuString::formatSize($file['size']) . ')'
			);
		}

		$tmpl	= 'core/view/form/FormElement_Select_Options.tmpl';
		$data	= array(
			'options'	=> $options,
			'value'		=> array()
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>