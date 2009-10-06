<?php

class TodoyuAssetsTasktabActionController extends TodoyuActionController {

	public function uploadformAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuAssetRenderer::renderUploadForm($idTask);
	}

	public function listAction(array $params) {
		$idTask	= intval($params['task']);


		return TodoyuAssetRenderer::renderList($idTask);
	}

	public function tabAction(array $params) {
		$idTask	= intval($params['task']);

		return TodoyuAssetRenderer::renderTabContent($idTask);
	}

}

?>



