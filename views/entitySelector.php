<?php
/* @var $this EntitySelector */

$url = Yii::app()->assetManager->publish(__DIR__.'/entity-selector.js', false, null, YII_DEBUG);
$cs = Yii::app()->clientScript;
$cs->registerScriptFile($url);
$cs->registerPackage('select2');

$data = $this->getDataJS();
?>

<? 
echo CHtml::openTag('span', ['id' => $this->id, 'class' => 'entity-selector' ]);
?>
	<? 
	$name = $this->name ?: CHtml::resolveName($this->model, $this->attrib);
	echo CHtml::hiddenField($name, $this->getAttribValue(), ['class' => 'es-value']);
	echo CHtml::textField($this->id.'-edit', '', ['class' => 'es-field']); 
	?>
	<? if (isset($data['entity']['link'])): ?>
	<? echo CHtml::link('<i class="icon-user"></i>', $data['entity']['link'], ['class' => 'btn es-link', 'target' => '_blank']); ?>
	<? endif; ?>
	
<?
echo CHtml::closeTag('span');
$cs->registerScript($this->id, "\$('#".$this->id."').entitySelector(".json_encode($data).");"); 
?>