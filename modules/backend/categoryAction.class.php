<?php

/**
 * Index action
 *
 * @author andrew(at)w(dot)cn
 * @since 23:16 01/04/2012
 * @todo thumb, detail
 */
defined('SYS_ROOT') || die('Access deined');

class categoryAction extends publicAction {

	public function showIndex() {
		// get models
		$sql = "SELECT model_id, IF(alias_name != '', alias_name, model_name) AS model_name FROM @__model ORDER BY model_id";
		$this->assign('models', db()->rows($sql));
		//print_r(db()->rows($sql));exit;
		// get parent model id
		if (!empty($_GET['parent_id']) && is_numeric($_GET['parent_id'])) {
			$rst = db()->row("SELECT model_id FROM @__category WHERE cat_id = :parent_id", array('parent_id' => $_GET['parent_id']));
			$parent_model_id = $rst['model_id'];
		} else {
			$parent_model_id = 0;
			$_GET['parent_id'] = 0;
		}
		$this->assign('parent_model_id', $parent_model_id);
		$this->assign('parent_id', $_GET['parent_id']);
		// get current level category
		$this->assign('categories', db()->rows("SELECT * FROM @__category WHERE parent_id = :parent_id ORDER BY sort, cat_id", array('parent_id' => $_GET['parent_id'])));
		outputJSON(1, $this->fetch());
	}

	public function doAddCategory() {
		// required: cat_name model_id
		if (empty($_POST['cat_name'])) {
			outputJSON(0, 'Invalid category name');
		}
		if (empty($_POST['model_id']) || !is_numeric($_POST['model_id'])) {
			outputJSON(0, 'Invalid model');
		}
		if (empty($_POST['parent_id']) || !is_numeric($_POST['parent_id'])) {
			$_POST['parent_id'] = 0;
		}
		if (empty($_POST['sort']) || !is_numeric($_POST['sort'])) {
			$_POST['sort'] = 0;
		}
		if (!isset($_POST['description'])) {
			$_POST['description'] = '';
		}
		if (!isset($_POST['url'])) {
			$_POST['url'] = '';
		}
		if (empty($_POST['status']) || !is_numeric($_POST['status'])) {
			$_POST['status'] = 0;
		}
		// check duplication, condition: cat_name, parent_id
		$sql = "SELECT COUNT(1) AS n FROM @__category WHERE cat_name = ':cat_name' AND parent_id = :parent_id";
		$rows = db()->rows($sql, array('cat_name' => $_POST['cat_name'], 'parent_id' => $_POST['parent_id']));
		if ($rows[0]['n']) {
			outputJSON(0, 'Category duplication');
		}
		$_POST['upper_id'] = $this->_parents($cat_id);
		$_POST['lower_id'] = $this->_children($cat_id);
		// insert table
		$sql = "INSERT INTO @__category SET cat_name = ':cat_name', model_id = :model_id, parent_id = :parent_id, sort = :sort, description = ':description', url = ':url', status = :status";
		$cat_id = db()->insert($sql, $_POST);
		// @todo 循环添加到父级 lowerid
		if ($cat_id) {
			$upper_id = $this->_parents($cat_id);
			$lower_id = $this->_children($cat_id);
			db()->execute("UPDATE @__category SET upper_id = ':upper_id', lower_id = ':lower_id' WHERE cat_id = :cat_id", array('upper_id' => $upper_id, 'lower_id' => $lower_id, 'cat_id' => $cat_id));
			$uppers = explode(',', $upper_id);
			array_pop($uppers);
			foreach ($uppers as $u) {
				db()->execute("UPDATE @__category SET lower_id = CONCAT(lower_id, ',', :cat_id) WHERE cat_id = :u", array('cat_id' => $cat_id, 'u' => $u));
			}
			outputJSON(1, $cat_id);
		} else {
			outputJSON(0, 'Add category failed');
		}
	}

	public function doRemoveCategory($return = FALSE) {
		if (empty($_POST['cat_id']) || !is_numeric($_POST['cat_id']) || $_POST['cat_id'] < 1) {
			if ($return)
				return FALSE;
			outputJSON(0, 'Invalid category id');
		}
		// get lower id
		$lower_id = db()->field("SELECT lower_id FROM @__category WHERE cat_id = :cat_id", $_POST);
		$lowers = explode(',', $lower_id);
		$all_upper_id = array();
		foreach ($lowers as $l) {
			// get upper id
			$upper_id = db()->field("SELECT upper_id FROM @__category WHERE cat_id = :cat_id", array('cat_id' => $l));
			$uppers = explode(',', $upper_id);
			// get model name
			$model_name = db()->field("SELECT model_name FROM @__model m, @__category c WHERE c.model_id = m.model_id AND c.cat_id = :cat_id", array('cat_id' => $l));
			if (!$model_name) {
				if ($return)
					return FALSE;
				outputJSON(0, 'Invalid model');
			}
			// remove content
			db()->execute("DELETE FROM @__content_:model_name WHERE content_id IN (SELECT content_id FROM @__content WHERE cat_id = :cat_id)", array('model_name' => $model_name, 'cat_id' => $l));
			db()->execute("DELETE FROM @__content WHERE cat_id = :cat_id", array('cat_id' => $l));
			// remove category
			db()->execute("DELETE FROM @__category WHERE cat_id = :cat_id", array('cat_id' => $l));
			// move current cat id from parents
			foreach ($uppers as $u) {
				$all_upper_id[] = $u;
				$lower_id = db()->field("SELECT lower_id FROM @__category WHERE cat_id = $u");
				$lower_id = explode(',', $lower_id);
				$index = array_search($_POST['cat_id'], $lower_id);
				if (is_numeric($index)) {
					array_splice($lower_id, $index, 1);
					db()->execute("UPDATE @__category SET lower_id = '" . implode(',', $lower_id) . "' WHERE cat_id = $u");
				}
			}
		}
		// refresh content count
		foreach ($all_upper_id as $aui) {
			$num = $this->_contentNumber($aui);
			$sql = "UPDATE @__category SET content_number = $num WHERE cat_id = $aui";
			db()->execute($sql);
		}
		if ($return)
			return TRUE;
		outputJSON(1);
	}

	public function doUpdateCategoryAttr() {
		if (empty($_POST['cat_id']) || !is_numeric($_POST['cat_id'])) {
			outputJSON(0, 'Invalid category id');
		}
		if (empty($_POST['attr_name'])) {
			outputJSON(0, 'Invalid attribute name');
		}
		$sql = "UPDATE @__category SET :attr_name = ':attr_value' WHERE cat_id = :cat_id";
		$result = db()->execute($sql, $_POST);
		outputJSON($result ? 1 : 0, 'Update ' . $_POST['attr_name'] . ' ' . ($result ? 'success' : 'failed'));
	}

	private function _children($cat_id) {
		$children = $cat_id;
		$sql = "SELECT cat_id FROM @__category WHERE parent_id = :parent_id ORDER BY sort, cat_id";
		$rows = db()->rows($sql, array('parent_id' => $cat_id));
		foreach ($rows as $r) {
			$children .= ',' . $this->_children($r['cat_id']);
		}
		return $children;
	}

	private function _parents($cat_id) {
		$parents = $cat_id;
		$sql = "SELECT parent_id FROM @__category WHERE cat_id = :cat_id";
		$parent_id = db()->field($sql, array('cat_id' => $cat_id));
		if ($parent_id)
			$parents = $this->_parents($parent_id) . ',' . $parents;
		return $parents;
	}

	private function _contentNumber($cat_id) {
		$sql = "SELECT COUNT(1) AS n FROM @__content WHERE cat_id IN (SELECT lower_id FROM @__category WHERE cat_id = $cat_id)";
		$rows = db()->rows($sql);
		return $rows[0]['n'];
	}

}