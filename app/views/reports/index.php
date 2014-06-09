<?php
/* @var $this ReportsController */

$baseUrl = Yii::app()->baseUrl;
//$cs = Yii::app()->getClientScript();
//$cs->registerScriptFile($baseUrl . '/js/admin/index/dialogs.js', CClientScript::POS_END);

$this->pageTitle = 'TAG - ' . Yii::t('default', 'Reports');
$this->breadcrumbs = array(
    Yii::t('default', 'Reports'),
);
?>

<div class="row-fluid">
    <div class="span12">
        <h3 class="heading-mosaic"><?php echo Yii::t('default', 'Reports'); ?></h3>  
    </div>
</div>

<div class="innerLR home">
    <div class="row-fluid">
        <?php if (Yii::app()->user->hasFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo Yii::app()->user->getFlash('success') ?>
            </div>
            <br/>
        <?php endif ?>
        <div class="span6">
            <div class="row-fluid">
                <div class="span3">
                    <a href="<?php echo Yii::app()->homeUrl; ?>?r=reports/BFReport" class="widget-stats">
                        <span class="glyphicons user"><i></i></span>
                        <span class="txt">Bolsa Família</span>
                        <div class="clearfix"></div>
                    </a>
                </div>
<!--                <div class="span3">
                    <a href="#" class="widget-stats" onclick='$("#import-file-dialog").dialog("open");'>
                        <span class="glyphicons database_plus"><i></i></span>
                        <span class="txt">Importar dados</span>
                        <div class="clearfix"></div>
                    </a>
                </div>
                <div class="span3">
                    <a href="<?php echo Yii::app()->homeUrl; ?>?r=admin/clearDB" class="widget-stats">
                        <span class="glyphicons database_minus"><i></i></span>
                        <span class="txt">Limpar Banco</span>
                        <div class="clearfix"></div>
                    </a>
                </div>
                <div class="span3">
                    <a href="<?php echo Yii::app()->homeUrl; ?>?r=admin/createUser" class="widget-stats">
                        <span class="glyphicons user"><i></i></span>
                        <span class="txt">Cadastrar usuário</span>
                        <div class="clearfix"></div>
                    </a>
                </div>-->
            </div>
        </div>
        <div class="span6">
<!--            <div class="span3">
                <a href="<?php echo Yii::app()->homeUrl; ?>?r=admin/export" class="widget-stats">
                    <span class="glyphicons file_export"><i></i></span>
                    <span class="txt">Exportar</span>
                    <div class="clearfix"></div>
                </a>
            </div>-->
        </div>
    </div>
</div>