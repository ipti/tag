<?php

$baseScriptUrl = Yii::app()->controller->module->baseScriptUrl;

$cs = Yii::app()->getClientScript();
$cs->registerCssFile($baseScriptUrl . '/common/css/layout.css');
$cs->registerScriptFile($baseScriptUrl . '/common/js/quiz.js', CClientScript::POS_END);
$this->setPageTitle('TAG - ' . Yii::t('default', 'Group'));


$form = $this->beginWidget('CActiveForm', array(
    'id' => 'group-form',
    'enableAjaxValidation' => false,
));
?>

<div class="row-fluid  hidden-print">
    <div class="span12">
        <h3 class="heading-mosaic"><?php echo $title; ?></h3>  
        <div class="buttons">
            <?php echo CHtml::htmlButton('<i></i>' . ($group->isNewRecord ? Yii::t('default', 'Create') : Yii::t('default', 'Save')), array('id' => 'save_group_button', 'class' => 'btn btn-icon btn-primary last glyphicons circle_ok', 'type' => 'button'));
            ?>
            <?php 
                if(!$group->isNewRecord){
                    echo CHtml::htmlButton('<i></i>' . Yii::t('default', 'Delete'), array('id' => 'delete_group_button', 'class' => 'btn btn-icon btn-primary last glyphicons delete', 'type' => 'button'));
                }
            ?>
        </div>
    </div>
</div>

<div class="innerLR">
    <?php if (Yii::app()->user->hasFlash('success') && (!$group->isNewRecord)): ?>
        <div class="alert alert-success">
            <?php echo Yii::app()->user->getFlash('success') ?>
        </div>
    <?php endif ?>

    <?php if (Yii::app()->user->hasFlash('error') && (!$group->isNewRecord)): ?>
        <div class="alert alert-error">
            <?php echo Yii::app()->user->getFlash('error') ?>
        </div>
    <?php endif ?>
    
    <div class="widget widget-tabs border-bottom-none">
        <div class="widget-head  hidden-print">
            <ul class="tab-classroom">
                <li id="tab-group" class="active" ><a class="glyphicons adress_book" href="#group" data-toggle="tab"><i></i><?php echo Yii::t('default', 'Group') ?></a></li>
            </ul>
        </div>

        <div class="widget-body form-horizontal">
            <div class="tab-content">
                    
                <div class="tab-pane active" id="group">
                        <div class="row-fluid">
                            <div class=" span5">
                                <div class="control-group">                
                                    <?php echo $form->labelEx($group, 'name', array('class' => 'control-label')); ?>
                                    <div class="controls">
                                        <?php echo $form->textField($group, 'name', array('size' => 60, 'maxlength' => 150)); ?>
                                        <span style="margin: 0;" class="btn-action single glyphicons circle_question_mark" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo Yii::t('default', 'Group Name'); ?>"><i></i></span>
                                        <?php echo $form->error($group, 'name'); ?>
                                    </div>
                                </div> 
                                <!-- .control-group -->
                                <div class="control-group">
                                    <?php echo $form->labelEx($group, 'quiz_id', array('class' => 'control-label required')); ?>
                                    <div class="controls">
                                    <?php
                                        $quizs = Quiz::model()->findAll(
                                            "status = :status AND final_date >= :final_date",
                                            [
                                                ':status' => 1,
                                                ':final_date' => date('Y-m-d'),
                                            ]
                                        );

                                        echo $form->dropDownList($group, 'quiz_id',
                                            CHtml::listData(
                                                $quizs, 'id', 'name'),
                                            array("prompt" => "Selecione um Questionário", 'class' => 'select-search-on')); ?>
                                        <?php echo $form->error($group, 'quiz_id'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
</div>

<?php $form = $this->endWidget(); ?>