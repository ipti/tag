<div id="mainPage" class="main"  style="margin-top:40px; padding: 10px">
    <?php
    $this->setPageTitle('TAG - ' . Yii::t('default', 'Create a new User'));
    $title = Yii::t('default', 'Create a new User');
    $contextDesc = Yii::t('default', 'Available actions that may be taken on User.');
    $this->menu = array(
        array('label' => Yii::t('default', 'List User'),
            'url' => array('index'),
            'description' => Yii::t('default', 'This action list all User, you can search, delete and update')),
    );
    ?>
    <style type="text/css">
        .widget-timeline .widget-body:before{background: none !important;}
        .widget-timeline ul.list-timeline li{margin-bottom: 2px;}
    </style>
   <div class="clearfix"></div>
    <div class="widget widget-4 widget-tabs-icons-only widget-timeline margin-bottom-none">

        <div class="widget-body">
            <div style="background-color: orange" class="alert alert-primary">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>Aviso Importante! Esta escola possuí pendências para exportação de dados para o EDUCACENSO</strong>
            </div>
        </div>

        <?php if (Yii::app()->user->hasFlash('success')){?>
            <div class="alert alert-success">
                <?php echo Yii::app()->user->getFlash('success') ?>
            </div>
        <?php }else if(Yii::app()->user->hasFlash('error')){?>
            <div class="alert alert-error">
                <?php echo Yii::app()->user->getFlash('success') ?>
            </div>
        <?php }else{?>
            <div>
                <a href="<?= CHtml::normalizeUrl(array('censo/export'))?>" class="btn btn-primary btn-icon glyphicons refresh"><i></i> <?= Yii::t('default', 'Export Now') ?></a>
            </div>
        <?php }?>


        <!-- Widget Heading END -->
        <?php 
            $dataValidation = []; 
            $timeExpiration = 60*60*12;
        ?>
        <div class="widget-body">
            <div class="tab-content">

                <!-- Filter Users Tab -->
                <div class="tab-pane active" id="filterUsersTab">
                    <ul class="list-timeline">
                        <h3>Pendências Escola</h3>
                        <?php foreach ($log['school']['validate']['identification']  as $index => $identification) {?>
                            <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                <span class="ellipsis">
                                    <?php echo SchoolIdentification::model()->getAttributeLabel(key($identification)) ?> -
                                    <?php echo current($identification) ?>
                                    <?php echo CHtml::link('- Corrigir',array('school/update', 'id'=>$log['school']['info']['inep_id'], 'censo' => 1)); ?>
                                </span>
                                    <?php $dataValidation['scholl'.$log['school']['info']['inep_id']][] = current($identification); ?>
                                <div class="clearfix"></div>
                            </li>
                        <?php } ?>

                        <?php foreach ($log['classroom']  as $index => $class) {?>
                            <?php if(!empty($class['validate']['identification'])){ ?>
                                <h5>Turma - <?php echo $class['info']['name']; ?></h5>
                                <?php foreach ($class['validate']['identification']  as $eindex => $classerror) {?>
                                    <li>
                                    <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                        <i></i>
                                    </span>
                                        <span class="ellipsis">
                                            <?php echo Classroom::model()->getAttributeLabel(key($classerror)) ?> -
                                            <?php echo current($classerror) ?>
                                            <?php echo CHtml::link('- Corrigir',array('classroom/update',
                                                'id'=>$class['info']['id'], 'censo' => 1)); ?>
                                        </span>
                                        <?php $dataValidation['classroom'.$class['info']['id']][] = current($classerror); ?>
                                        <div class="clearfix"></div>
                                    </li>
                                <?php }?>
                            <?php }?>
                        <?php }?>

                        <?php foreach ($log['instructor']  as $index => $instructor) {?>

                            <h5>Professor - <?php echo $instructor['info']['name']; ?></h5>
                            <?php foreach ($instructor['validate']['identification']  as $instructorerror) {?>
                                <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                    <span class="ellipsis">
                                        <?php echo InstructorIdentification::model()->getAttributeLabel(key($instructorerror)) ?> - <?php echo current($instructorerror) ?>
                                        <?php echo CHtml::link('- Corrigir',array('instructor/update',
                                            'id'=>$instructor['info']['id'], 'censo' => 1)); ?>
                                    </span>
                                    <?php $dataValidation['instructor'.$instructor['info']['id']][] = current($instructorerror); ?>
                                    <div class="clearfix"></div>
                                </li>
                            <?php }?>
                            <?php foreach ($instructor['validate']['documents']  as $instructorerror) {?>
                                <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                    <span class="ellipsis">
                                        <?php echo InstructorDocumentsAndAddress::model()->getAttributeLabel(key($instructorerror)) ?> - <?php echo current($instructorerror) ?>
                                        <?php echo CHtml::link('- Corrigir',array('instructor/update',
                                            'id'=>$instructor['info']['id'], 'censo' => 1)); ?>
                                    </span>
                                    <?php $dataValidation['instructor'.$instructor['info']['id']][] = current($instructorerror); ?>
                                    <div class="clearfix"></div>
                                </li>
                            <?php }?>
                            <?php foreach ($instructor['validate']['variabledata']  as $variabledata) {?>
                                <?php foreach ($variabledata['errors'] as $vberros) {?>
                                <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                    <span class="ellipsis">Na turma <?php echo $variabledata['turma'] ?> |
                                        <?php echo InstructorVariableData::model()->getAttributeLabel(key($vberros)) ?> - <?php echo current($vberros) ?>
                                        <?php echo CHtml::link('- Corrigir',array('classroom/update',
                                            'id'=>$variabledata['id'], 'censo' => 1)); ?>
                                    </span>
                                    <?php $dataValidation['classroom'.$variabledata['id']][] = current($vberros); ?>
                                    <div class="clearfix"></div>
                                </li>
                                <?php }?>
                            <?php }?>
                        <?php }?>
                        <?php foreach ($log['student']  as $student) {?>
                            <h5>Aluno - <?php echo $student['info']['name']; ?></h5>
                            <?php foreach ($student['validate']['identification']  as $studenterror) {?>
                                <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                    <span class="ellipsis">
                                        <?php echo StudentIdentification::model()->getAttributeLabel(key($studenterror)) ?> -
                                        <?php echo current($studenterror) ?>
                                        <?php echo CHtml::link('- Corrigir',array('student/update',
                                            'id'=>$student['info']['id'], 'censo' => 1)); ?>
                                    </span>
                                    <?php $dataValidation['student'.$student['info']['id']][] = current($studenterror); ?>
                                    <div class="clearfix"></div>
                                </li>
                            <?php }?>
                            <?php foreach ($student['validate']['documents']  as $studenterror) {?>
                                <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                    <span class="ellipsis">
                                        <?php echo StudentDocumentsAndAddress::model()->getAttributeLabel(key($studenterror)) ?> - <?php echo current($studenterror) ?>
                                        <?php echo CHtml::link('- Corrigir',array('student/update',
                                            'id'=>$student['info']['id'], 'censo' => 1)); ?>
                                    </span>
                                    <?php $dataValidation['student'.$student['info']['id']][] = current($studenterror); ?>
                                    <div class="clearfix"></div>
                                </li>
                            <?php }?>
                            <?php foreach ($student['validate']['enrollment']  as $enrollment) {?>
                                <?php foreach ($enrollment['errors'] as $eberros) {?>
                                    <li>
                                <span style="width: 20px;text-align: center;color:white;font-weight: bold" class="glyphicons activity-icon">
                                    <i></i>
                                </span>
                                        <span class="ellipsis">Na turma <?php echo $enrollment['turma'] ?> |
                                            <?php echo StudentEnrollment::model()->getAttributeLabel(key($eberros)) ?> - <?php echo current($eberros) ?>
                                            <?php echo CHtml::link('- Corrigir',array('enrollment/update',
                                                'id'=>$enrollment['id'], 'censo' => 1)); ?>
                                        </span>
                                        <?php $dataValidation['enrollment'.$enrollment['id']][] = current($eberros); ?>
                                        <div class="clearfix"></div>
                                    </li>
                                <?php }?>
                            <?php }?>
                        <?php }?>

                    </ul>
                </div>
                <!-- // Filter Users Tab END -->
                <?php
                    foreach ($dataValidation as $key => $value) {
                        Yii::app()->cache->set($key, $value, $timeExpiration);
                    }
                ?>


            </div>
        </div>
    </div>

</div>
