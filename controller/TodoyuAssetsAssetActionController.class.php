<?php

class TodoyuAssetsAssetActionController extends TodoyuActionController {

	public function downloadAction(array $params) {
		$idAsset	= intval($params['asset']);

		TodoyuAssetManager::downloadAsset($idAsset);
	}

	public function deleteAction(array $params) {
		$idAsset	= intval($params['asset']);

		TodoyuAssetManager::deleteAsset($idAsset);
	}

	public function togglevisibilityAction(array $params) {
		$idAsset	= intval($params['asset']);

		TodoyuAssetManager::toggleVisibility($idAsset);
	}
}


?>