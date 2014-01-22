<?php

class InstructorController extends Controller {
    
   //@TODO s2 - Tirar Aba Dados do Instrutor do update de instrutor
   //@TODO s2 - Adicionar validações em todos os campos que erstão faltando
   //@TODO s2 - validar CPF


    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = 'fullmenu';
    private $InstructorIdentification = 'InstructorIdentification';
    private $InstructorDocumentsAndAddress = 'InstructorDocumentsAndAddress';
    private $InstructorVariableData = 'InstructorVariableData';
    private $InstructorTeachingData = 'InstructorTeachingData';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'view', 'create', 'update', 'getCity'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {      
        $this->render('view', array(
            'modelInstructorIdentification' => $this->loadModel($id, $this->InstructorIdentification),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $modelInstructorIdentification = new InstructorIdentification();
        $modelInstructorDocumentsAndAddress = new InstructorDocumentsAndAddress();
        $modelInstructorVariableData = new InstructorVariableData();
        $saveInstructor = false;
        $saveDocumentsAndAddress = false;
        $saveVariableData = false;

        $error[] = '';
        if (isset($_POST['InstructorIdentification'], $_POST['InstructorDocumentsAndAddress']
                        , $_POST['InstructorVariableData'])) {
            $modelInstructorIdentification->attributes = $_POST['InstructorIdentification'];
            $modelInstructorDocumentsAndAddress->attributes = $_POST['InstructorDocumentsAndAddress'];
            $modelInstructorVariableData->attributes = $_POST['InstructorVariableData'];
            
            if (!isset($modelInstructorIdentification->edcenso_nation_fk)) {
                $modelInstructorIdentification->edcenso_nation_fk = 76;
            }
            if (!isset($modelInstructorIdentification->edcenso_uf_fk)) {
                $modelInstructorIdentification->edcenso_uf_fk = 0;
            }
            if (!isset($modelInstructorIdentification->edcenso_city_fk)) {
                $modelInstructorIdentification->edcenso_city_fk = 0;
            }


            $saveInstructor = true;

            //=== MODEL DocumentsAndAddress
            if (isset($modelInstructorDocumentsAndAddress->cep) && !empty($modelInstructorDocumentsAndAddress->cep)) {
                //Então o endereço, uf e cidade são obrigatórios
                if (isset($modelInstructorDocumentsAndAddress->address) && !empty($modelInstructorDocumentsAndAddress->address) &&
                        isset($modelInstructorDocumentsAndAddress->neighborhood) && !empty($modelInstructorDocumentsAndAddress->neighborhood) &&
                        isset($modelInstructorDocumentsAndAddress->edcenso_uf_fk) && !empty($modelInstructorDocumentsAndAddress->edcenso_uf_fk) &&
                        isset($modelInstructorDocumentsAndAddress->edcenso_city_fk) && !empty($modelInstructorDocumentsAndAddress->edcenso_city_fk)) {

                    $saveDocumentsAndAddress = true;
                } else {
                    $error['documentsAndAddress'] = 'CEP preenchido então, o Endereço, Bairro, UF e Cidade são Obrigatórios !';
                }
            } else {
                $saveDocumentsAndAddress = true;
            }
            //======================================
            //=== MODEL VariableData            
            if (isset($modelInstructorVariableData->scholarity) &&
                    $modelInstructorVariableData->scholarity == 6) {

                if (isset($modelInstructorVariableData->high_education_situation_1, $modelInstructorVariableData->high_education_course_code_1_fk, $modelInstructorVariableData->high_education_institution_type_1, $modelInstructorVariableData->high_education_institution_code_1_fk)
                        || isset($modelInstructorVariableData->high_education_situation_2, $modelInstructorVariableData->high_education_course_code_2_fk, $modelInstructorVariableData->high_education_institution_type_2, $modelInstructorVariableData->high_education_institution_code_2_fk)
                        || isset($modelInstructorVariableData->high_education_situation_3, $modelInstructorVariableData->high_education_course_code_3_fk, $modelInstructorVariableData->high_education_institution_type_3, $modelInstructorVariableData->high_education_institution_code_3_fk)) {
                    $saveVariableData = true;
                } else {
                    $error['variableData'] = "Pelo menos uma situação do curso superior, código
do curso superior, tipo de instituição e instituição
do curso superior deverão ser obrigatoriamente
preenchidos";
                }
            } else {
                $saveVariableData = true;
            }

            if ($saveInstructor && $saveDocumentsAndAddress && $saveVariableData) {

                // Setar todos os school_inep_id
                $modelInstructorIdentification->school_inep_id_fk = Yii::app()->user->school;
                $modelInstructorDocumentsAndAddress->school_inep_id_fk = $modelInstructorIdentification->school_inep_id_fk;
                $modelInstructorVariableData->school_inep_id_fk = $modelInstructorIdentification->school_inep_id_fk;

                if ($modelInstructorIdentification->validate()
                        && $modelInstructorDocumentsAndAddress->validate()
                        && $modelInstructorVariableData->validate()
                        && $modelInstructorIdentification->save()) {
                    $modelInstructorDocumentsAndAddress->id = $modelInstructorIdentification->id;
                    $modelInstructorVariableData->id = $modelInstructorIdentification->id;
// CORRIGIR !!!!!!!!!!!!!
                    
                    $modelInstructorDocumentsAndAddress->edcenso_uf_fk = $modelInstructorIdentification->edcenso_uf_fk;
                    $modelInstructorDocumentsAndAddress->edcenso_city_fk = $modelInstructorIdentification->edcenso_city_fk;

                    if ($modelInstructorDocumentsAndAddress->save()
                            && $modelInstructorVariableData->save()) {
                        Yii::app()->user->setFlash('success', Yii::t('default', 'Professor adicionado com sucesso!'));
                        $this->redirect(array('index'));
                    }
                }
            }
        }

        $this->render('create', array(
            'modelInstructorIdentification' => $modelInstructorIdentification,
            'modelInstructorDocumentsAndAddress' => $modelInstructorDocumentsAndAddress,
            'modelInstructorVariableData' => $modelInstructorVariableData,
            'error' => $error,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        //=======================================
        $modelInstructorIdentification = $this->loadModel($id, $this->InstructorIdentification);
        $modelInstructorDocumentsAndAddress = $this->loadModel($id, $this->InstructorDocumentsAndAddress);
        $modelInstructorVariableData = $this->loadModel($id, $this->InstructorVariableData);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($modelStudentIdentification);   

        $saveInstructor = false;
        $saveDocumentsAndAddress = false;
        $saveVariableData = false;

        //==================================
        
        $error[] = '';
        if (isset($_POST['InstructorIdentification'], $_POST['InstructorDocumentsAndAddress']
                        , $_POST['InstructorVariableData'])) {
            $modelInstructorIdentification->attributes = $_POST['InstructorIdentification'];
            $modelInstructorDocumentsAndAddress->attributes = $_POST['InstructorDocumentsAndAddress'];
            $modelInstructorVariableData->attributes = $_POST['InstructorVariableData'];
            
            if (!isset($modelInstructorIdentification->edcenso_nation_fk)) {
                $modelInstructorIdentification->edcenso_nation_fk = 76;
            }
            if (!isset($modelInstructorIdentification->edcenso_uf_fk)) {
                $modelInstructorIdentification->edcenso_uf_fk = 0;
            }
            if (!isset($modelInstructorIdentification->edcenso_city_fk)) {
                $modelInstructorIdentification->edcenso_city_fk = 0;
            }


            $saveInstructor = true;

            //=== MODEL DocumentsAndAddress
            if (isset($modelInstructorDocumentsAndAddress->cep) && !empty($modelInstructorDocumentsAndAddress->cep)) {
                //Então o endereço, uf e cidade são obrigatórios
                if (isset($modelInstructorDocumentsAndAddress->address) && !empty($modelInstructorDocumentsAndAddress->address) &&
                        isset($modelInstructorDocumentsAndAddress->neighborhood) && !empty($modelInstructorDocumentsAndAddress->neighborhood) &&
                        isset($modelInstructorDocumentsAndAddress->edcenso_uf_fk) && !empty($modelInstructorDocumentsAndAddress->edcenso_uf_fk) &&
                        isset($modelInstructorDocumentsAndAddress->edcenso_city_fk) && !empty($modelInstructorDocumentsAndAddress->edcenso_city_fk)) {

                    $saveDocumentsAndAddress = true;
                } else {
                    $error['documentsAndAddress'] = 'CEP preenchido então, o Endereço, Bairro, UF e Cidade são Obrigatórios !';
                }
            } else {
                $saveDocumentsAndAddress = true;
            }
            //======================================
            //=== MODEL VariableData            
            if (isset($modelInstructorVariableData->scholarity) &&
                    $modelInstructorVariableData->scholarity == 6) {

                if (isset($modelInstructorVariableData->high_education_situation_1, $modelInstructorVariableData->high_education_course_code_1_fk, $modelInstructorVariableData->high_education_institution_type_1, $modelInstructorVariableData->high_education_institution_code_1_fk)
                        || isset($modelInstructorVariableData->high_education_situation_2, $modelInstructorVariableData->high_education_course_code_2_fk, $modelInstructorVariableData->high_education_institution_type_2, $modelInstructorVariableData->high_education_institution_code_2_fk)
                        || isset($modelInstructorVariableData->high_education_situation_3, $modelInstructorVariableData->high_education_course_code_3_fk, $modelInstructorVariableData->high_education_institution_type_3, $modelInstructorVariableData->high_education_institution_code_3_fk)) {
                    $saveVariableData = true;
                } else {
                    $error['variableData'] = "Pelo menos uma situação do curso superior, código
do curso superior, tipo de instituição e instituição
do curso superior deverão ser obrigatoriamente
preenchidos";
                }
            } else {
                $saveVariableData = true;
            }

            if ($saveInstructor && $saveDocumentsAndAddress && $saveVariableData) {

                // Setar todos os school_inep_id
                $modelInstructorDocumentsAndAddress->school_inep_id_fk = $modelInstructorIdentification->school_inep_id_fk;
                $modelInstructorVariableData->school_inep_id_fk = $modelInstructorIdentification->school_inep_id_fk;

                if ($modelInstructorIdentification->validate()
                        && $modelInstructorDocumentsAndAddress->validate()
                        && $modelInstructorVariableData->validate()
                        && $modelInstructorIdentification->save()) {
                    $modelInstructorDocumentsAndAddress->id = $modelInstructorIdentification->id;
                    $modelInstructorVariableData->id = $modelInstructorIdentification->id;
// CORRIGIR !!!!!!!!!!!!!
                    
                    $modelInstructorDocumentsAndAddress->edcenso_uf_fk = $modelInstructorIdentification->edcenso_uf_fk;
                    $modelInstructorDocumentsAndAddress->edcenso_city_fk = $modelInstructorIdentification->edcenso_city_fk;

                    if ($modelInstructorDocumentsAndAddress->save()
                            && $modelInstructorVariableData->save()) {
                        Yii::app()->user->setFlash('success', Yii::t('default', 'Professor alterado com sucesso!'));
                        $this->redirect(array('index'));
                    }
                }
            }
        }
         
        //====================================
        $this->render('update', array(
            'modelInstructorIdentification' => $modelInstructorIdentification,
            'modelInstructorDocumentsAndAddress' => $modelInstructorDocumentsAndAddress,
            'modelInstructorVariableData' => $modelInstructorVariableData,
            'error' => $error,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        
        $modelInstructorIdentification = $this->loadModel($id, $this->InstructorIdentification);
        $modelInstructorDocumentsAndAddress = $this->loadModel($id, $this->InstructorDocumentsAndAddress);
        $modelInstructorVariableData = $this->loadModel($id, $this->InstructorVariableData);
        $modelInstructorTeachingData = $this->loadModel($id, $this->InstructorTeachingData);

        if ( $modelInstructorDocumentsAndAddress->delete()
                && $modelInstructorVariableData->delete()
                && $modelInstructorTeachingData->delete()
                && $modelInstructorIdentification->delete()) {
            
            Yii::app()->user->setFlash('success', Yii::t('default', 'Professor excluído com sucesso!'));
            $this->redirect(array('index'));
        } else {
            throw new CHttpException(404, 'The requested page does not exist.');
        }


//            if( $this->loadModel($id, $this->SCHOOL_STRUCTURE)->delete()
//                && $this->loadModel($id, $this->SCHOOL_IDENTIFICATION)->delete()){
//                    Yii::app()->user->setFlash('success', Yii::t('default', 'Escola excluída com sucesso:'));
//                    $this->redirect(array('index'));
//            }else{
//                throw new CHttpException(404,'The requested page does not exist.');
//            }
//        
//        if (Yii::app()->request->isPostRequest) {
//            // we only allow deletion via POST request
//            $this->loadModel($id)->delete();
//
//            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
//            if (!isset($_GET['ajax']))
//                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
//        }
//        else
//            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $filter = new InstructorIdentification('search');
        $filter->unsetAttributes();  // clear any default values
        if (isset($_GET['InstructorIdentification'])) {
            $filter->attributes = $_GET['InstructorIdentification'];
        }
        $school = Yii::app()->user->school;
        $dataProvider = new CActiveDataProvider('InstructorIdentification',
                        array('criteria'=>array(
                                'condition'=>'school_inep_id_fk='.$school,
                                'order'=>'name ASC',
                            ),'pagination' => array(
                                'pageSize' => 12,
                        )));
        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'filter' => $filter,
        ));
    }

  

    //Método para Solicitação Ajax para o select UF x Cities

    public function actionGetCity() {
        
        $edcenso_uf_fk = $_POST['edcenso_uf_fk'];
        
        $data = EdcensoCity::model()->findAll('edcenso_uf_fk=:uf_id', array(':uf_id' => (int) $edcenso_uf_fk));
        $data = CHtml::listData($data, 'id', 'name');

        echo CHtml::tag('option', array('value' => 'NULL'), 'Selecione a Cidade', true);
        foreach ($data as $value => $name) {
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
        }  
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $modelInstructorIdentification = new InstructorIdentification('search');
        $modelInstructorDocumentsAndAddress = new InstructorDocumentsAndAddress('search');
        $modelInstructorVariableData = new InstructorVariableData('search');
        $modelInstructorTeachingData = new InstructorTeachingData('search');

        $modelInstructorIdentification->unsetAttributes();  // clear any default values
        $modelInstructorDocumentsAndAddress->unsetAttributes();  // clear any default values
        $modelInstructorVariableData->unsetAttributes();  // clear any default values
        $modelInstructorTeachingData->unsetAttributes();  // clear any default values
        if (isset($_GET[$this->InstructorIdentification], $_GET[$this->InstructorDocumentsAndAddress]
                        , $_GET[$this->InstructorVariableData], $_GET[$this->InstructorTeachingData])) {
            $modelInstructorIdentification->attributes = $_GET['InstructorIdentification'];
            $modelInstructorDocumentsAndAddress->attributes = $_GET['InstructorDocumentsAndAddress'];
            $modelInstructorVariableData->attributes = $_GET['InstructorVariableData'];
            $modelInstructorTeachingData->attributes = $_GET['InstructorTeachingData'];
        }



        $this->render('admin', array(
            'modelInstructorIdentification' => $modelInstructorIdentification,
            'modelInstructorDocumentsAndAddress' => $modelInstructorDocumentsAndAddress,
            'modelInstructorVariableData' => $modelInstructorVariableData,
            'modelInstructorTeachingData' => $modelInstructorTeachingData
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id, $model) {

        $instructor = InstructorIdentification::model()->findByPk($id);
        $instructor_inepid_id = isset($instructor->inep_id) && !empty($instructor->inep_id)?$instructor->inep_id:$instructor->id;
        $return = null;
        if ($model == $this->InstructorIdentification) {
            $return = InstructorIdentification::model()->findByPk($id);
        } else if ($model == $this->InstructorDocumentsAndAddress) {
            if(isset($instructor->inep_id) && !empty($instructor->inep_id)){
                $return = InstructorDocumentsAndAddress::model()->findByAttributes(
                    array('inep_id' => $instructor_inepid_id));
            }else{
                $return = InstructorDocumentsAndAddress::model()->findByPk($instructor_inepid_id);
            }
        } else if ($model == $this->InstructorVariableData) {
              if(isset($instructor->inep_id) && !empty($instructor->inep_id)){
                $return = InstructorVariableData::model()->findByAttributes(
                    array('inep_id' => $instructor_inepid_id));
            }else{
                $return = InstructorVariableData::model()->findByPk($instructor_inepid_id);
            }
        } else if ($model == $this->InstructorTeachingData) {
              if(isset($instructor->inep_id) && !empty($instructor->inep_id)){ // VEr possível correção !!!!
                $return = InstructorTeachingData::model()->findByAttributes(
                    array('inep_id' => $instructor_inepid_id));
            }else{
                $return = InstructorTeachingData::model()->findByPk($instructor_inepid_id);
            }
            
        }

        if ($return === null && $model == $this->InstructorIdentification) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $return;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'instructor-identification-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
