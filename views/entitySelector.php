<?php
/* @var $this EntitySelector */

$url = Yii::app()->assetManager->publish(__DIR__.'/entity-selector.js', false, null, YII_DEBUG);
$cs = Yii::app()->clientScript;
$cs->registerScriptFile($url);
$cs->registerScriptFile('/js/select2/select2.js');
$cs->registerScriptFile('/js/select2/select2_locale_ru.js');
$cs->registerCssFile('/js/select2/select2.css');
$cs->registerCssFile('/js/select2/select2-bootstrap.css');

$data = $this->getDataJS();
?>

<? 
echo CHtml::openTag('div', ['id' => $this->id, 'class' => 'entity-selector' ]);
?>
	<? 
	$name = $this->name ?: CHtml::resolveName($this->model, $this->attrib);
	echo CHtml::hiddenField($name, $this->getAttribValue(), ['class' => 'es-value']);
	echo CHtml::textField($this->id.'-edit', '', ['class' => 'es-field']); 
	?>
	<? if (isset($data['entity']['link'])): ?>
	<? echo CHtml::link('<i class="icon-user"></i>', $data['entity']['link'], ['class' => 'btn es-link', 'target' => '_blank']); ?>
	<? endif; ?>
	<? if (FALSE && $this->showItemClear): ?>
	<? echo CHtml::link('<i class="icon-remove">&nbsp;</i>', null, ['class' => 'btn']); ?>
	<? endif; ?>
	
<?
echo CHtml::closeTag('div');
$cs->registerScript($this->id, "\$('#".$this->id."').entitySelector(".json_encode($data).");"); 
?>