<?php

class TodoyuAssetsZipActionController extends TodoyuActionController {

	public function downloadAction(array $params) {
		$idTask		= intval($params['task']);
		$assetIDs	= TodoyuDiv::intExplode(',', $params['assets'], true, true);

		if( sizeof($assetIDs) > 0 ) {
			TodoyuAssetManager::downloadAssetsZipped($idTask, $assetIDs);
		} else {
			die("NO ASSETS SELECTED");
		}
	}

}


?>