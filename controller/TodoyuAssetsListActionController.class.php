<?php

class TodoyuAssetsListActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		$idTask		= intval($params['task']);

		return TodoyuAssetRenderer::renderList($idTask);
	}

}


?>