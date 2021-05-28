<div id="mainPage" class="main container-instructor">
    <?php
    $this->setPageTitle('TAG - ' . Yii::t('default', 'Instructor Identifications'));
    $contextDesc = Yii::t('default', 'Available actions that may be taken on InstructorIdentification.');
    $this->menu = array(
        array('label' => Yii::t('default', 'Create a new InstructorIdentification'), 'url' => array('create'), 'description' => Yii::t('default', 'This action create a new InstructorIdentification')),
    );
    ?>

    <div class="row-fluid box-instructor">
        <div class="span12">
            <h3 class="heading-mosaic"><?php echo Yii::t('default', 'Instructor Identifications') ?></h3>
            <div class="buttons span7 hide-responsive">
                <a href="<?php echo Yii::app()->createUrl('instructor/updateEmails')?>" class="btn btn-primary btn-icon glyphicons envelope"><i></i> Atualizar e-mails</a>
                <a href="<?php echo Yii::app()->createUrl('instructor/create')?>" class="btn btn-primary btn-icon glyphicons circle_plus"><i></i> Adicionar professor</a>
            </div>
        </div>
        <div class="btn-group pull-right mt-30 responsive-menu dropdown-margin">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                Menu
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo Yii::app()->createUrl('instructor/updateEmails')?>"><i></i> Atualizar e-mails</a></li>
                <li><a href="<?php echo Yii::app()->createUrl('instructor/create')?>"><i></i> Adicionar professor</a></li>
            </ul>
        </div>
    </div> 

    <div class="innerLR">
        <?php if (Yii::app()->user->hasFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo Yii::app()->user->getFlash('success') ?>
            </div>
            <br/>
        <?php endif ?>
        <div class="widget">
            <div class="widget-body">
                <?php
                $this->widget('zii.widgets.grid.CGridView', array(
                    'dataProvider' => $filter->search(),
                    'enablePagination' => true,
                    'filter' => $filter,
                    'itemsCssClass' => 'table table-condensed table-striped table-hover table-primary table-vertical-center checkboxs',
                    'columns' => array(
                        array(
                            'name' => 'name',
                            'type' => 'raw',
                            'value' => 'CHtml::link($data->name,Yii::app()->createUrl("instructor/update",array("id"=>$data->id)))',
                            'htmlOptions' => array('width'=> '400px')
                        ),
                        array(
                            'name' => 'documents',
                            'header' => 'CPF',
                            'value' => '$data->documents->cpf',
                            'htmlOptions' => array('width'=> '400px')
                        ),
                        array(
                            'name' => 'birthday_date',
                            'filter' => false
                        ),
                        ),
                ));
                ?>
            </div>
        </div>
    </div>
</div>