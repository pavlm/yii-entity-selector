<?php
/**
 * 
 * выбор из списка сущностей
 * @author pavlm
 *
 */
class EntitySelector extends CWidget
{
	/**
	 * @var CActiveRecord model wich contain link to entity
	 */
	public $model;
	
	/**
	 * @var string model attribute name with entity link 
	 */
	public $attrib;
	
	/**
	 * @var string hidden html input name
	 */
	public $name;
	
	/**
	 * @var string active record entity class name wich listed in dropdown
	 */
	public $itemType;
	
	/**
	 * @var array|CDbCriteria - predefined entity criteria
	 */
	public $itemCriteria;
	
	/**
	 * @var Closure - returns criteria to filter entities by query (first parameter)
	 */
	public $itemSearchCriteria;

	/**
	 * @var string entity id field name
	 */
	public $itemId = 'id';
	
	/**
	 * @var string|Closure for list labels generation
	 */
	public $itemLabel = 'name';
	
	/**
	 * @var string|Closure shows ui link to selected item
	 */
	public $itemLink;
	
	/**
	 * @var boolean TODO check
	 */
	public $showItemLink = false;
	
	/**
	 * @var boolean TODO check
	 */
	public $showItemClear = false;
	
	/**
	 * @var int - size of dataset for server paging, if zero - all records loaded  
	 */
	public $listPageSize = 30;
	
	/**
	 * @var string optional route for EntitySelectorAjaxAction
	 */
	public $ajaxRoute;
	
	/**
	 * @var string partial view where EntitySelector widget rendered (in case of ajaxRoute using)
	 */
	public $ajaxView;
	
	/**
	 * @var array - additional plugin options
	 */
	public $jsOptions = array();
	
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
		$val = $this->model ? $this->model->{$this->attrib} : null;
		if ($val instanceof CActiveRecord)
			$val = $val->{$this->itemId};
		return $val;
	}
	
	public function getJSOptions()
	{
		$val = $this->getAttribValue();
		$e = $val ? $this->formatEntity($this->loadEntity($val)) : false;
		$data = array(
			'ajaxId' => $this->ajaxId,
			'value' => $val,
		    'ajaxUrl' => $this->ajaxRoute ? Yii::app()->createUrl($this->ajaxRoute) : null,
		    'ajaxView' => $this->ajaxRoute ? $this->ajaxView : null,
		    'listPageSize' => $this->listPageSize,
			'entity' => $e,
		);
		$data = array_merge($data, $this->jsOptions);
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
		$query = @$_REQUEST['query'];
		$page = intval(@$_REQUEST['page']);
		if (!empty($query)) {
		    if ($this->itemSearchCriteria) {
		        // search by user criteria
    		    $searchFunc = $this->itemSearchCriteria; 
    		    $cr->mergeWith($searchFunc($query));
		    } elseif (is_string($this->itemLabel)) {
		        // search by label
		        $cr->addSearchCondition($this->itemLabel, $query);
		    }
		}
		if ($this->listPageSize) {
		    $cr->limit = $this->listPageSize;
		    $cr->offset = $this->listPageSize * $page;
		}
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