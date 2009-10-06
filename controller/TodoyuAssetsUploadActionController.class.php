<?php

class TodoyuAssetsUploadActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		$idTask		= intval($params['asset']['id_task']);

		$tempFile	= $_FILES['asset']['tmp_name']['file'];
		$fileName	= $_FILES['asset']['name']['file'];
		$mimeType	= $_FILES['asset']['type']['file'];

		$idAsset	= TodoyuAssetManager::addTaskAsset($idTask, $tempFile, $fileName, $mimeType);

		return TodoyuAssetRenderer::renderUploadframeContent($idTask, $fileName);
	}

}


?>



