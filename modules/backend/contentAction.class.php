<?php

/**
 * Index action
 *
 * @author andrew(at)w(dot)cn
 * @since 23:16 01/04/2012
 * @todo thumb, detail
 */
defined('SYS_ROOT') || die('Access deined');

class contentAction extends publicAction {
	/**
	 * page offset
	 *
	 * @access private
	 * @var integer
	 */
	private $_offset = 5;

	public function showIndex() {
		if (empty($_GET['cat_id']) || !is_numeric($_GET['cat_id'])) {
			outputJSON(0, 'Invalid category id');
		}
		// get model id
		$model_id = db()->field("SELECT model_id FROM @__category WHERE cat_id = :cat_id", $_GET);
		if (!$model_id) {
			outputJSON(0, 'Invalid model id');
		}
		// get model_name
		$model_name = db()->field("SELECT model_name FROM @__model WHERE model_id = $model_id");
		if (!$model_name) {
			outputJSON(0, 'Invalid model name');
		}
		// format start,offset var
		$start = max(0, $_GET['start']? (int)$_GET['start']: 0);
		if (!isset($_GET['offset']) || (int)$_GET['offset'] < 1) {
			$offset = $this->_offset;
		}
		$offset = (int)$_GET['offset'];
		// get model fields
		$rows = db()->rows("SELECT field, alias FROM @__model WHERE model_id = $model_id AND show_on_list = 1 ORDER BY sort, model_field_id");
		$aliases = array();
		foreach ($rows as $f) {
			$aliases[$f['field']] = $f['alias'];
		}
		$this->assign('aliases', $aliases);
		outputJSON(1, $this->fetch());
	}

	public function showList() {
		if (empty($_GET['cat_id']) || !is_numeric($_GET['cat_id'])) {
			outputJSON(0, 'Invalid category id');
		}
		// get model id
		$model_id = db()->field("SELECT model_id FROM @__category WHERE cat_id = :cat_id", $_GET);
		if (!$model_id) {
			outputJSON(0, 'Invalid model id');
		}
		// get model_name
		$model_name = db()->field("SELECT model_name FROM @__model WHERE model_id = $model_id");
		if (!$model_name) {
			outputJSON(0, 'Invalid model name');
		}
		// format start,offset var
		$start = max(0, $_GET['start']? (int)$_GET['start']: 0);
		if (!isset($_GET['offset']) || (int)$_GET['offset'] < 1) {
			$offset = $this->_offset;
		}
		$offset = (int)$_GET['offset'];
		// get model fields
		$rows = db()->rows("SELECT field, alias, is_system FROM @__model WHERE model_id = $model_id AND show_on_list = 1 ORDER BY sort, model_field_id");
		$fields = '';
		foreach ($rows as $f) {
			$fields .= ($fields?',':'').($f['is_system']? 'c':'ext').'.'. $f;
		}
		$sql = "SELECT $fields FROM @__content c, @__content_:model_name ext WHERE c.content_id = ext.content_id ORDER BY sort, content DESC LIMIT :start, :offset";
		$result = db()->rows($sql, array('model_name'=>$model_name, 'start'=>$start, 'offset'=>$offset));
		outputJSON(1, $result);
	}
}