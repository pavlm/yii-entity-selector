<?php
/**
 * 
 * Selector dedicated ajax channel, loads specified selector view
 * @author pavlm
 *
 */
class EntitySelectorAjaxAction extends CAction
{
    public $selectorWidgetAlias = 'ext.yii-entity-selector.EntitySelector';
    
    /**
     * @var string - partial view with selector widget, with all options 
     */
    public $ajaxView;
    
    /**
     * @var string security suffix
     */
    public $ajaxViewSuffix = '_selector';
    
    public function run() {
        
        if (!$this->ajaxView) {
            $this->ajaxView = @$_REQUEST['ajaxView'];
            if (!$this->ajaxView)
                throw new CHttpException(500, 'no view');
            $this->ajaxView .= $this->ajaxViewSuffix;
        }
        $this->controller->renderPartial($this->ajaxView);
        Yii::app()->end();
        
    }
   
}