<?php

/**
 * Index action
 *
 * @author andrew(at)w(dot)cn
 * @since 23:16 01/04/2012
 */
defined('SYS_ROOT') || die('Access deined');

class modelAction extends publicAction {

	/**
	 * Data types
	 *
	 * @access private
	 * @var array
	 */
	private $_data_types = array(
		'TINYINT',
		'SMALLINT',
		'MEDIUMINT',
		'INT',
		'BIGINT',
		'DECIMAL',
		'FLOAT',
		'DOUBLE',
		'REAL',
		'BIT',
		'BOOLEAN',
		'SERIAL',
		'DATE',
		'DATETIME',
		'TIMESTAMP',
		'TIME',
		'YEAR',
		'CHAR',
		'VARCHAR',
		'TINYTEXT',
		'TEXT',
		'MEDIUMTEXT',
		'LONGTEXT',
		'BINARY',
		'VARBINARY',
		'TINYBLOB',
		'MEDIUMBLOB',
		'BLOB',
		'LONGBLOB',
		'ENUM',
		'SET',
	);

	/**
	 * Element types
	 *
	 * @access private
	 * @var array
	 */
	private $_element_types = array(
		'TEXT',
		'PASSWORD',
		'CHECKBOX',
		'RADIO',
		'SELECT',
		'MULTISELECT',
		'TEXTAREA',
		'EDITOR',
		'FILE',
		'MULTIFILE',
	);

	public function showImport() {
		$model_name = $_GET['model_name'];
		// model_id
		$model_id = db()->field("SELECT model_id FROM @__model WHERE model_name = '$model_name'");
		$rows = db()->rows("SELECT COUNT(1) AS n FROM @__model_field WHERE model_id = $model_id");
		if ($rows[0]['n'])
			die('done!');
		// content
		$sql = "SHOW COLUMNS FROM @__content";
		$content = db()->rows($sql);
		// model
		$sql = "SHOW COLUMNS FROM @__content_{$model_name}";
		$model = db()->rows($sql);
		// insert
		foreach ($content as $c) {
			$sql = "INSERT INTO @__model_field SET model_id = :model_id, `field` = ':field', data_type = ':data_type', length = ':length',is_system=1";
			if (strpos($c['Type'], '(')) {
				$data_type = strtoupper(substr($c['Type'], 0, strpos($c['Type'], '(')));
				$length = preg_replace('/.*?\((.*?)\).*/is', '$1', $c['Type']);
			} else {
				$data_type = strtoupper($c['Type']);
				$length = '';
			}
			db()->execute($sql, array('model_id' => $model_id, 'field' => $c['Field'], 'data_type' => $data_type, 'length' => $length));
		}
		foreach ($model as $c) {
			if ($c['Field'] == 'content_id')
				continue;
			$sql = "INSERT INTO @__model_field SET model_id = :model_id, `field` = ':field', data_type = ':data_type', length = ':length'";
			if (strpos($c['Type'], '(')) {
				$data_type = strtoupper(substr($c['Type'], 0, strpos($c['Type'], '(')));
				$length = preg_replace('/.*?\((.*?)\).*/is', '$1', $c['Type']);
			} else {
				$data_type = strtoupper($c['Type']);
				$length = '';
			}
			db()->execute($sql, array('model_id' => $model_id, 'field' => $c['Field'], 'data_type' => $data_type, 'length' => $length));
		}
		echo 'done!';
	}

	public function showIndex() {
		$this->assign('models', db()->rows("SELECT * FROM @__model ORDER BY model_id"));
		outputJSON(1, $this->fetch());
	}

	public function doAddModel() {
		// check model name
		if (empty($_POST['model_name']))
			outputJSON('Invalid model name');
		// check alias namewrite
		if (empty($_POST['alias_name']))
			outputJSON('Invalid alias name');
		// check status
		$_POST['status'] = empty($_POST['alias_name']) ? 0 : 1;
		// check description
		$_POST['description'] = empty($_POST['description']) ? 0 : $_POST['description'];
		// check exists
		$sql = "SELECT COUNT(1) AS n FROM @__model WHERE model_name = ':model_name' OR alias_name = ':alias_name'";
		$rows = db()->rows($sql, $_POST);
		if ($rows[0]['n'])
			outputJSON(0, 'Model duplication');
		$sql = "INSERT INTO @__model SET model_name = ':model_name', alias_name = ':alias_name', status = :status, description = ':description'";
		$model_id = db()->insert($sql, $_POST);
		// add model table
		if (!$model_id)
			outputJSON(0, 'Add model failed');
		$sql = "CREATE TABLE `libox`.`@__content_:model_name`( `content_id` INT UNSIGNED NOT NULL , PRIMARY KEY (`content_id`) )";
		db()->execute($sql, array('model_name' => $_POST['model_name']));
		// add model field
		$sql = "SHOW COLUMNS FROM @__content";
		$content = db()->rows($sql);
		// insert
		foreach ($content as $c) {
			$sql = "INSERT INTO @__model_field SET model_id = :model_id, `field` = ':field', data_type = ':data_type', length = ':length',is_system=1";
			if (strpos($c['Type'], '(')) {
				$data_type = strtoupper(substr($c['Type'], 0, strpos($c['Type'], '(')));
				$length = preg_replace('/.*?\((.*?)\).*/is', '$1', $c['Type']);
			} else {
				$data_type = strtoupper($c['Type']);
				$length = '';
			}
			db()->execute($sql, array('model_id' => $model_id, 'field' => $c['Field'], 'data_type' => $data_type, 'length' => $length));
		}
		outputJSON(1, $model_id);
	}

	public function doRemoveModel() {
		if (empty($_POST['model_id']) || !is_numeric($_POST['model_id']) || $_POST['model_id'] < 1)
			outputJSON(0, 'Invalid model id');
		$model_name = db()->field("SELECT model_name FROM @__model WHERE model_id = :model_id", array('model_id' => $_POST['model_id']));
		if (empty($model_name))
			outputJSON(0, 'Invalid model id');
		// drop model extend table
		if (db()->execute("DROP TABLE @__content_:model_name", array('model_name' => $model_name))) {
			// remove model content/category
			$cats = db()->rows("SELECT cat_id FROM @__category WHERE model_id = :model_id", array('model_id' => $_POST['model_id']));
			//db()->execute("DELETE FROM @__content WHERE cat_id IN (SELECT cat_id FROM @__category WHERE model_id = :model_id)", array('model_id' => $_POST['model_id']));
			//db()->execute("DELETE FROM @__category WHERE model_id = :model_id", array('model_id' => $_POST['model_id']));
			$category = action('category');
			foreach ($cats as $cat) {
				$_POST['cat_id'] = $cat['cat_id'];
				$category->doRemoveCategory(TRUE);
			}
			// remove model setting
			db()->execute("DELETE FROM @__model_field WHERE model_id = :model_id", array('model_id' => $_POST['model_id']));
			db()->execute("DELETE FROM @__model WHERE model_id = :model_id", array('model_id' => $_POST['model_id']));
			outputJSON(1);
		} else {
			outputJSON(0, 'Invalid model table');
		}
	}

	public function doUpdateModelAttr() {
		if (empty($_POST['model_id']) || !is_numeric($_POST['model_id']) || $_POST['model_id'] < 1)
			outputJSON(0, 'Invalid model id');
		if (empty($_POST['attr_name']))
			outputJSON(0, 'Invalid attr name');
		if (!isset($_POST['attr_value']))
			outputJSON(0, 'Invalid attr value');
		if ($_POST['attr_name'] == 'model_name') {
			if (!preg_match('/^[a-z0-9_]+$/is', $_POST['attr_value']))
				outputJSON('Invalid model name');
			// check target model / table exists
			$sql = "SELECT COUNT(1) AS n FROM @__model WHERE model_name = ':model_name'";
			$rows = db()->rows($sql, array('model_name' => $_POST['attr_value']));
			if ($rows[0]['n'])
				outputJSON(0, 'Model duplication');
			$sql = "SHOW TABLES LIKE '@__content_:model_name'";
			$rows = db()->rows($sql, array('model_name' => $_POST['attr_value']));
			if (count($rows) > 0)
				outputJSON(0, 'Model table duplication');
			// change table name
			$model_name = db()->field('SELECT model_name FROM @__model WHERE model_id = :model_id', array('model_id' => $_POST['model_id']));
			$result = db()->execute("RENAME TABLE @__content_:model_name TO @__content_:attr_value", array('model_name' => $model_name, 'attr_value' => $_POST['attr_value']));
			if (!$result)
				outputJSON(0, 'Update failed' . print_r(db()->logs, TRUE));
		}
		$sql = "UPDATE @__model SET `:attr_name` = ':attr_value' WHERE model_id = :model_id";
		$result = db()->execute($sql, $_POST);
		outputJSON($result ? 1 : 0, 'Update ' . ($result ? 'success' : 'failed'));
	}

	// @todo show by id and import exists model
	public function showFields() {
		// get model id from $_GET
		if (empty($_GET['model_id']) || !is_numeric($_GET['model_id']))
			outputJSON(0, 'Invalid model id');
		$sql = "SELECT * FROM @__model_field WHERE model_id = :model_id AND is_system = %d ORDER BY model_field_id";
		$this->assign('model_fields_basic', db()->rows(sprintf($sql, 1), array('model_id' => $_GET['model_id'])));
		$this->assign('model_fields_extend', db()->rows(sprintf($sql, 0), array('model_id' => $_GET['model_id'])));
		$this->assign('model_id', $_GET['model_id']);
		$this->assign('data_types', $this->_data_types);
		$this->assign('types', $this->_element_types);
		outputJSON(1, $this->fetch('field'));
	}

	public function doSubmitField() {
		// parse params
		if (empty($_POST['model_id']))
			outputJSON(0, 'Invalid model id');
		$_POST['model_id'] = (int) $_POST['model_id'];
		if ($_POST['model_id'] < 1)
			outputJSON(0, 'Invalid model id');
		if (empty($_POST['field']))
			outputJSON(0, 'Invalid field');
		if (empty($_POST['alias']))
			outputJSON(0, 'Invalid alias');
		$_POST['sort'] = max((int) (empty($_POST['sort']) ? 0 : $_POST['sort']), 0);
		if (empty($_POST['data_type']) || !in_array(strtoupper($_POST['data_type']), $this->_data_types))
			outputJSON(0, 'Invalid data type');
		if (in_array(strtoupper($_POST['data_type']), array('CHAR', 'VARCHAR', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL', 'BIT'))) {
			if (empty($_POST['length']) || !is_numeric($_POST['length']) || $_POST['length'] < 1)
				outputJSON(0, 'Invalid length');
			else
				$_POST['length'] = '(' . $_POST['length'] . ')';
		} else
			$_POST['length'] = '';
		if (empty($_POST['type']) || !in_array(strtoupper($_POST['type']), $this->_element_types))
			outputJSON(0, 'Invalid type');
		if (empty($_POST['extend_value']))
			$_POST['extend_value'] = '';
		if (!isset($_POST['default']))
			$_POST['default'] = '';
		if (empty($_POST['style_class']))
			$_POST['style_class'] = '';
		if (empty($_POST['rule']))
			$_POST['rule'] = '';
		if (empty($_POST['event']))
			$_POST['event'] = '';
		if (empty($_POST['show_on_list']))
			$_POST['show_on_list'] = 0;
		else
			$_POST['show_on_list'] = 1;
		if (empty($_POST['show_on_form']))
			$_POST['show_on_form'] = 0;
		else
			$_POST['show_on_form'] = 1;
		// unsigned
		if (in_array(strtoupper($_POST['data_type']), array('TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL')))
			$_POST['unsigned'] = 'unsigned';
		// fields string
		$fields = "alias=':alias', sort=:sort, data_type=':data_type', `length`=':length', `type`=':type', extend_value=':extend_value', `default`=':default', style_class=':style_class', rule=':rule', `event`=':event', show_on_list=:show_on_list, show_on_form=:show_on_form";
		// set model_id
		$model_id = $_POST['model_id'];
		// get model name
		$model_name = db()->field("SELECT model_name FROM @__model WHERE model_id=:model_id", array('model_id' => $model_id));
		// initialize return status
		$return = FALSE;
		// when add new one
		if (!empty($_POST['isNew'])) {
			// check duplication
			$sql = "SELECT COUNT(1) AS n FROM @__model_field WHERE model_id=:model_id AND field=':field'";
			$rows = db()->rows($sql, array('model_id' => $_POST['model_id'], 'field' => $_POST['field']));
			if ($rows[0]['n'])
				outputJSON(0, 'Duplicate field');
			$sql = "INSERT INTO @__model_field SET model_id=:model_id, `field`=':field', %s";
			unset($_POST['isNew']);
			$model_field_id = db()->insert(sprintf($sql, $fields), array_merge($_POST, array('length' => str_replace(array('(', ')'), array('', ''), $_POST['length']))));
			if ($model_field_id)
				$return = TRUE;
			if ($return) {
				$return = db()->execute("ALTER TABLE @__content_:model_name ADD COLUMN :field :data_type:length :unsigned NOT NULL", array('model_name' => $model_name, 'field' => $_POST['field'], 'data_type' => $_POST['data_type'], 'length' => $_POST['length'], 'unsigned' => $_POST['unsigned']));
				if (!$return) {
					// loopback
					db()->execute('DELETE FROM @__model_field WHERE model_field_id=:model_field_id', array('model_field_id' => $model_field_id));
				}
			}
		} else { // when update
			// check duplication
			$sql = "SELECT model_field_id, data_type, length FROM @__model_field WHERE model_id=:model_id AND field=':field'";
			$row = db()->row($sql, array('model_id' => $_POST['model_id'], 'field' => $_POST['field']));
			$model_field_id = $row['model_field_id'];
			if (!$model_field_id)
				outputJSON(0, 'Invalid model field');
			$sql = "UPDATE @__model_field SET %s WHERE model_field_id=:model_field_id";
			$_POST['model_field_id'] = $model_field_id;
			$return = db()->execute(sprintf($sql, $fields), array_merge($_POST, array('length' => str_replace(array('(', ')'), array('', ''), $_POST['length']))));
			if ($return && empty($_POST['isBasic'])) {
				$return = db()->execute("ALTER TABLE @__content_:model_name CHANGE :field :field :data_type:length :unsigned NOT NULL", array('model_name' => $model_name, 'field' => $_POST['field'], 'data_type' => $_POST['data_type'], 'length' => $_POST['length'], 'unsigned' => $_POST['unsigned']));
				if (!$return) {
					db()->execute("UPDATE @__model_field SET data_type=':data_type', length=':length' WHERE model_field_id=:model_field_id", $row);
				}
			}
		}
		outputJSON($return ? 1 : 0, 'Process ' . ($return ? 'seccess' : 'failed'), db()->logs);
	}

	public function doRemoveField() {
		empty($_POST['model_id']) && outputJSON(0, 'Invalid model id');
		empty($_POST['field']) && outputJSON(0, 'Invalid field');
		$model_id = (int) $_POST['model_id'];
		$field = $_POST['field'];
		//remove from database
		$sql = "DELETE FROM @__model_field WHERE model_id = :model_id AND field = ':field'";
		if (db()->execute($sql, array('model_id' => $model_id, 'field' => $field))) {
			// get model name from table
			$model_name = db()->field("SELECT model_name FROM @__model WHERE model_id = :model_id", array('model_id' => $model_id));
			// delete from model table
			$sql = "ALTER TABLE @__content_:model_name DROP COLUMN :field";
			db()->execute($sql, array('model_name' => $model_name, 'field' => $field));
		}
		outputJSON(1);
	}

}