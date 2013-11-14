<?php
/* @var $this PvPnController */
/* @var $model PvPn */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Parts',
);

$this->menu=array(
	array('label'=>'Create Part', 'url'=>array('create')),
	array('label'=>'Manage Parts', 'url'=>array('admin')),
);
?>

<h1>Parts</h1>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'cgridview',
	'dataProvider' => $dataProvider,
	'filter' => $model,
	'columns' => array(
		array(
			'name'=> 'PNPartNumber',
            'htmlOptions'=>array('style'=>'width: 150px;'),
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->PNPartNumber), array(\'view\', \'id\' => $data->id))',
		),
        array(
			'name'=>'PNType',
			'type'=>'raw',
            'htmlOptions'=>array('style'=>'width: 50px; text-align: center;'),
			'value'=>'CHtml::encode($data->PNType)',
		),
        array(
			'name'=>'PNStatus',
			'type'=>'raw',
            'htmlOptions'=>array('style'=>'width: 50px; text-align: center;'),
			'value'=>'CHtml::encode($data->PNStatus)',
		),
        array(
			'name'=>'PNRevision',
			'type'=>'raw',
            'htmlOptions'=>array('style'=>'width: 50px; text-align: center;'),
			'value'=>'CHtml::encode($data->PNRevision)',
		),
		array(
			'name'=>'PNTitle',
            'htmlOptions'=>array('style'=>'width: 350px;'),
			'type'=>'raw',
			'value'=>'CHtml::encode($data->PNTitle)',
		),
		array(
			'name'=>'PNDetail',
			'type'=>'raw',
			'value'=>'CHtml::encode($data->PNDetail)',
		),/*
		array(
			'class'=>'CButtonColumn',
		),*/
	),
));

?>