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
    
    public function run() {
        
        if (!$this->ajaxView) {
            $this->ajaxView = @$_REQUEST['ajaxView'];
            if (!$this->ajaxView)
                $this->error('no view');
            $this->ajaxView .= '_selector'; // security suffix
        }
        
        $this->controller->renderPartial($this->ajaxView);
        Yii::app()->end();
        
    }
    
    public function error($msg, $httpCode=500) {
        http_response_code($httpCode);
        print $msg;
        Yii::app()->end();
    }
    
}