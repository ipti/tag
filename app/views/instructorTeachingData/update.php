<div id="mainPage" class="main">
    <?php
$this->breadcrumbs=array(
	'Instructor Teaching Datas'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

    $title=Yii::t('default', 'Update InstructorTeachingData: ');
    $contextDesc = Yii::t('default', 'Available actions that may be taken on InstructorTeachingData.');
    $this->menu=array(
    array('label'=> Yii::t('default', 'Create a new InstructorTeachingData'), 'url'=>array('create'),'description' => Yii::t('default', 'This action create a new InstructorTeachingData')),
    array('label'=> Yii::t('default', 'List InstructorTeachingData'), 'url'=>array('index'),'description' => Yii::t('default', 'This action list all Instructor Teaching Datas, you can search, delete and update')),
    );  
    ?>

    <div class="twoColumn">
        <div class="columnone" style="padding-right: 1em">
            <?php echo $this->renderPartial('_form', array('model'=>$model, 
                'error' => $error,'title'=>$title)); ?>        </div>
        <div class="columntwo">
        </div>
    </div>
</div>