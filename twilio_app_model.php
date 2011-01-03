<?php

class TwilioAppModel extends AppModel {

	public $useDbConfig = 'twilio';
	
/**
 * Copied straight out of CakePHP core for save testing
 *
 * Saves model data (based on white-list, if supplied) to the database. By
 * default, validation occurs before save.
 *
 * @param array $data Data to save.
 * @param mixed $validate Either a boolean, or an array.
 *   If a boolean, indicates whether or not to validate before saving.
 *   If an array, allows control of validate, callbacks, and fieldList
 * @param array $fieldList List of fields to allow to be written
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @access public
 * @link http://book.cakephp.org/view/1031/Saving-Your-Data
 */
	function save($data = null, $validate = true, $fieldList = array()) {
		$defaults = array('validate' => true, 'fieldList' => array(), 'callbacks' => true);
		$_whitelist = $this->whitelist;
		$fields = array();

		if (!is_array($validate)) {
			$options = array_merge($defaults, compact('validate', 'fieldList', 'callbacks'));
		} else {
			$options = array_merge($defaults, $validate);
		}

		if (!empty($options['fieldList'])) {
			$this->whitelist = $options['fieldList'];
		} elseif ($options['fieldList'] === null) {
			$this->whitelist = array();
		}
		$this->set($data);

		if (empty($this->data) && !$this->hasField(array('created', 'updated', 'modified'))) {
			return false;
		}

		foreach (array('created', 'updated', 'modified') as $field) {
			$keyPresentAndEmpty = (
				isset($this->data[$this->alias]) &&
				array_key_exists($field, $this->data[$this->alias]) &&
				$this->data[$this->alias][$field] === null
			);
			if ($keyPresentAndEmpty) {
				unset($this->data[$this->alias][$field]);
			}
		}

		$exists = $this->exists();
		$dateFields = array('modified', 'updated');

		if (!$exists) {
			$dateFields[] = 'created';
		}
		if (isset($this->data[$this->alias])) {
			$fields = array_keys($this->data[$this->alias]);
		}
		if ($options['validate'] && !$this->validates($options)) {
			$this->whitelist = $_whitelist;
			return false;
		}

		$db =& ConnectionManager::getDataSource($this->useDbConfig);

		foreach ($dateFields as $updateCol) {
			if ($this->hasField($updateCol) && !in_array($updateCol, $fields)) {
				$default = array('formatter' => 'date');
				$colType = array_merge($default, $db->columns[$this->getColumnType($updateCol)]);
				if (!array_key_exists('format', $colType)) {
					$time = strtotime('now');
				} else {
					$time = $colType['formatter']($colType['format']);
				}
				if (!empty($this->whitelist)) {
					$this->whitelist[] = $updateCol;
				}
				$this->set($updateCol, $time);
			}
		}

		if ($options['callbacks'] === true || $options['callbacks'] === 'before') {
			$result = $this->Behaviors->trigger($this, 'beforeSave', array($options), array(
				'break' => true, 'breakOn' => false
			));
			if (!$result || !$this->beforeSave($options)) {
				$this->whitelist = $_whitelist;
				return false;
			}
		}
		
		// Had to check again after beforeSave because some resource models set dummy id's so that update is called
		$exists = $this->exists();

		if (empty($this->data[$this->alias][$this->primaryKey])) {
			unset($this->data[$this->alias][$this->primaryKey]);
		}
		
		/*$fields = $values = array();

		foreach ($this->data as $n => $v) {
			if (isset($this->hasAndBelongsToMany[$n])) {
				if (isset($v[$n])) {
					$v = $v[$n];
				}
				$joined[$n] = $v;
			} else {
				if ($n === $this->alias) {
					foreach (array('created', 'updated', 'modified') as $field) {
						if (array_key_exists($field, $v) && empty($v[$field])) {
							unset($v[$field]);
						}
					}
					pr($v);
					foreach ($v as $x => $y) {
						if ($this->hasField($x) && (empty($this->whitelist) || in_array($x, $this->whitelist))) {
							list($fields[], $values[]) = array($x, $y);
						}
					}
				}
			}
		}*/
		$fields = array_keys($this->data[$this->alias]);
		$values = array_values($this->data[$this->alias]);
		$count = count($fields);

		if (!$exists && $count > 0) {
			$this->id = false;
		}
		$success = true;
		$created = false;

		if ($count > 0) {
			$cache = $this->_prepareUpdateFields(array_combine($fields, $values));
			if (!empty($this->id)) {
				$success = (bool)$db->update($this, $fields, $values);
			} else {
				$fInfo = $this->_schema[$this->primaryKey];
				$isUUID = ($fInfo['length'] == 36 &&
					($fInfo['type'] === 'string' || $fInfo['type'] === 'binary')
				);
				if (empty($this->data[$this->alias][$this->primaryKey]) && $isUUID) {
					if (array_key_exists($this->primaryKey, $this->data[$this->alias])) {
						$j = array_search($this->primaryKey, $fields);
						$values[$j] = String::uuid();
					} else {
						list($fields[], $values[]) = array($this->primaryKey, String::uuid());
					}
				}

				if (!$db->create($this, $fields, $values)) {
					$success = $created = false;
				} else {
					$created = true;
				}
			}

			if ($success && !empty($this->belongsTo)) {
				$this->updateCounterCache($cache, $created);
			}
		}

		if (!empty($joined) && $success === true) {
			$this->__saveMulti($joined, $this->id, $db);
		}

		if ($success && $count > 0) {
			if (!empty($this->data)) {
				$success = $this->data;
			}
			if ($options['callbacks'] === true || $options['callbacks'] === 'after') {
				$this->Behaviors->trigger($this, 'afterSave', array($created, $options));
				$this->afterSave($created);
			}
			if (!empty($this->data)) {
				$success = Set::merge($success, $this->data);
			}
			$this->data = false;
			$this->_clearCache();
			$this->validationErrors = array();
		}
		$this->whitelist = $_whitelist;
		return $success;
	}
	
	/**
	 * Method overwrite of exists
	 *
	 * @access public
	 * @return mixed
	 */
	function exists() {
		return $this->getID();
	}

}

?>