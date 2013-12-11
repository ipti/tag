<div id="mainPage" class="main">
<?php
$this->breadcrumbs=array(
	'Student Enrollments',
);
$contextDesc = Yii::t('default', 'Available actions that may be taken on StudentEnrollment.');
$this->menu=array(
array('label'=> Yii::t('default', 'Create a new StudentEnrollment'), 'url'=>array('create'),'description' => Yii::t('default', 'This action create a new StudentEnrollment')),
); 

?>
<div class="twoColumn">
        <div class="columnone" style="padding-right: 1em">
            <?php if (Yii::app()->user->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <?php echo Yii::app()->user->getFlash('success') ?>
                </div>
                <br/>
            <?php endif ?>
            <div class="panelGroup form">
                <div class="panelGroupHeader"><div class=""><?php echo Yii::t('default', 'Student Enrollments')?></div></div>
                <div class="panelGroupBody">
                    <?php $this->widget('zii.widgets.grid.CGridView', array(
                        'dataProvider' => $dataProvider,
                        'enablePagination' => true,
                        'baseScriptUrl' => Yii::app()->theme->baseUrl . '/plugins/gridview/',
                        'columns' => array(
                    		'register_type',
		'school_inep_id_fk',
		'student_inep_id',
		'student_fk',
		'classroom_inep_id',
		'classroom_fk',
		'enrollment_id',
                            /*
		'unified_class',
		'edcenso_stage_vs_modality_fk',
		'another_scholarization_place',
		'public_transport',
		'transport_responsable_government',
		'vehicle_type_van',
		'vehicle_type_microbus',
		'vehicle_type_bus',
		'vehicle_type_bike',
		'vehicle_type_animal_vehicle',
		'vehicle_type_other_vehicle',
		'vehicle_type_waterway_boat_5',
		'vehicle_type_waterway_boat_5_15',
		'vehicle_type_waterway_boat_15_35',
		'vehicle_type_waterway_boat_35',
		'vehicle_type_metro_or_train',
		'student_entry_form',
		'id',
                             */
                     array('class' => 'CButtonColumn',),),
                    )); ?>
                </div>   
            </div>
        </div>
        <div class="columntwo">
        </div>
    </div>

</div>