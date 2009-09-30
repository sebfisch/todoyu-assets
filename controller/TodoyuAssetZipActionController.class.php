<?php

class TodoyuAssetZipActionController extends TodoyuActionController {

	public function downloadAction(array $params) {
		$idTask		= intval($params['task']);
		$assetIDs	= TodoyuDiv::intExplode(',', $params['assets'], true, true);
		
		TodoyuAssetManager::downloadAssetsZipped($idTask, $assetIDs);	
	}	
	
}


?>