yii-entity-selector
=========================================

Widget to replace native html select in case of choosing db entities from large lists.

Based on select2 jquery-plugin.  
Supports server search an paging.

Usage
-------------------

Dropdown list content loaded via ajax when select2 manipulated. There are two options to handle these ajax requests.

1) Ajax queries go to page where widget instantiated  
In this case conflicts may occur with other server ajax handlers.
To differentiate ajax requests to one page selector widget uses param $_REQUEST['ajaxId'].

```
    $this->widget('ext.yii-entity-selector.EntitySelector', array(
        'model' => $host, // model with link attribute 
        'attrib' => 'userId1', // attrib with link to user 
        'itemType' => 'User', // active record model class
        'itemLabel' => 'name', // list item label, may be closure
    ));

```

2) Ajax queries go via dedicated route  
In this case ajax conflicts avoided, but additional settings required.  
2.1) Register selector action on any controller

```
    public function actions() {
        return [
            'entitySelectorAjax' => 'ext.yii-entity-selector.EntitySelectorAjaxAction',
        ];
    }
```

2.2) Selector widget must be rendered in separate view.  
And three widget fields must be set: id, ajaxRoute, ajaxView. 

```
    $this->widget('ext.yii-entity-selector.EntitySelector',
        array(
            'id' => 'user-select',
            'ajaxRoute' => 'entitySelectorDemo/entitySelectorAjax', // route to EntitySelectorAjaxAction
            'ajaxView' => '//entitySelectorDemo/_user', // alias to view where this widget call resides
            'model' => $host,
            'attrib' => 'userId2',
            'itemType' => 'User',
            'itemLabel' => function($u){ return "{$u->id}. {$u->name} {$u->last_name}"; }, // composite list item label
            'itemSearchCriteria' => function($q){ $c = new CDbCriteria(); 
                $c->addSearchCondition('name', $q); $c->addSearchCondition('last_name', $q, true, 'OR'); return $c; 
            }, // if 'itemLabel' not a string then search criteria must be provided 
    ));
```


Installation
-----------------

1. Clone extension from github

    git clone https://github.com/pavlm/yii-entity-selector.git yoursite/protected/extensions/yii-entity-selector

2. There are no select2 plugin in this repo, you must include it before using yii-entity-selector, or register script package 'select2' (snippet below) 

```
    'clientScript' => array(
        'packages' => array(
            'select2' => array(
                'basePath' => 'webroot.css.select2',
                'baseUrl' => '/css/select2',
                'js' => array('select2.js', 'select2_locale_ru.js'),
                'css' => array('select2.css',),
                'depends' => array('jquery'),
            ),
        ),
    ),
```

