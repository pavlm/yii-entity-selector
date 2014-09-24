<?php
/**
 * 
 * выбор из списка сущностей
 *
 */
class EntitySelector extends CWidget
{
	/**
	 * @var CActiveRecord
	 */
	public $model;
	
	/**
	 * @var string
	 */
	public $attrib;
	
	/**
	 * @var string hidden html input name
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $itemType;
	
	/**
	 * @var array|CDbCriteria
	 */
	public $itemCriteria;

	/**
	 * @var string
	 */
	public $itemId = 'id';
	
	/**
	 * @var string|Closure
	 */
	public $itemLabel = 'name';
	
	/**
	 * @var string|Closure
	 */
	public $itemLink;
	
	/**
	 * @var boolean
	 */
	public $showItemLink = false;
	
	/**
	 * @var boolean
	 */
	public $showItemClear = false;
	
	const loadFull = 0;
	const loadPartial = 1;
	
	/**
	 * @var int
	 */
	public $loadType = 0;
	
	public function run()
	{
		if (Yii::app()->request->isAjaxRequest && @$_REQUEST['ajaxId'] == $this->ajaxId)
		{
			$this->actionAjaxLoad();
		}
		else 
		{
			$this->actionDraw();
		}
	}

	public function getAjaxId()
	{
		return $this->id.'-entity-selector';
	}
	
	public function getAttribValue() {
		$val = $this->model->{$this->attrib};
		if ($val instanceof CActiveRecord)
			$val = $val->{$this->itemId};
		return $val;
	}
	
	public function getDataJS()
	{
		$val = $this->getAttribValue();
		$e = $val ? $this->formatEntity($this->loadEntity($val)) : false;
		$data = array(
			'ajaxId' => $this->ajaxId,
			'value' => $val,
			'entity' => $e,
		);
		return $data;
	}
	
	public function actionDraw()
	{
		$this->render('entitySelector');
	}
	
	public function actionAjaxLoad()
	{
		while (@ob_end_clean()) {}
		$es = $this->loadEntities();
		$fes = $this->formatEntities($es);
		echo json_encode($fes);
		die();
	}
	
	public function loadEntities()
	{
		$cr = $this->itemCriteria ? (!is_array($this->itemCriteria) ? $this->itemCriteria : new CDbCriteria($this->itemCriteria)) : new CDbCriteria();
		$es = CActiveRecord::model($this->itemType)->findAll($cr);
		return $es;
	}
	
	public function loadEntity($pk)
	{
		$cr = $this->itemCriteria ? (!is_array($this->itemCriteria) ? $this->itemCriteria : new CDbCriteria($this->itemCriteria)) : new CDbCriteria();
		$e = CActiveRecord::model($this->itemType)->findByPk($pk, $cr);
		return $e;
	}
	
	public function formatEntities($es)
	{
		$fes = [];
		foreach ($es as $e) {
			$fes[] = $this->formatEntity($e);
		}
		return $fes;
	}
	
	public function formatEntity($e)
	{
		if (!$e)
			return $e;
		$itemLabel = $this->itemLabel;
		$fe = array(
			'id' => $e->{$this->itemId},
			'text' => is_callable($itemLabel) ? $itemLabel($e) : $e->{$itemLabel},
		);
		if (!empty($this->itemLink)) {
			$func = $this->itemLink;
			$fe['link'] = $func($e); 
		}
		return $fe;
	}
}