<?php

class ConfigurationController extends Controller {

	public function actionIndex() {
		$this->render('index');
	}
	
	public function actionSchool() {
		$year = Yii::app()->user->school;
		$model = SchoolConfiguration::model()->findByAttributes(array("school_inep_id_fk"=>$year));

		if(!isset($model))
			$model = new SchoolConfiguration;

		if (isset($_POST['SchoolConfiguration'])) {
			$model->setAttributes($_POST['SchoolConfiguration']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('index'));
			}
		}
		$this->render('school', array( 'model' => $model));
	}
	
	public function actionClassroom() {
		if(isset($_POST['Classrooms'])){
			$Classrooms_ids = $_POST['Classrooms'];
			$year = Yii::app()->user->year;
			foreach($Classrooms_ids as $id){
				$classroom = Classroom::model()->findByPk($id);
				$class_board = ClassBoard::model()->findAllByAttributes(array('classroom_fk'=>$id));
				$teaching_data = InstructorTeachingData::model()->findAllByAttributes(array('classroom_id_fk'=>$id));
	
				$newClassroom = new Classroom();
				$newClassroom->attributes = $classroom->attributes;
				$newClassroom->school_year = $year;
				$newClassroom->id = null;
				$newClassroom->inep_id = null;
 				$save = $newClassroom->save();		
 				if($save){
 					$save = true;
 					foreach ($class_board as $cb){
 						$newClassBorad = new ClassBoard();
 						$newClassBorad->attributes = $cb->attributes;
 						$newClassBorad->id = null;
 						$newClassBorad->classroom_fk = $newClassroom->id;
 						$save = $save && $newClassBorad->save();
 					}
 					foreach ($teaching_data as $td){
 						$newTeachingData = new InstructorTeachingData();
 						$newTeachingData->attributes = $td->attributes;
 						$newTeachingData->id = null;
 						$newTeachingData->classroom_id_fk =  $newClassroom->id;
 						$newTeachingData->classroom_inep_id = null;
 						$save = $save && $newTeachingData->save();
 					}
 				}
			}
			if($save)
				Yii::app()->user->setFlash('success', Yii::t('default', 'Turmas reutilizadas com sucesso!'));
			else
				Yii::app()->user->setFlash('error', Yii::t('default', 'Erro na reutilização das Turmas.'));
			$this->render('index');
		}
		$this->render('classrooms', array(
				'title' => Yii::t('default', 'Classroom Configurarion')
		));
	}
	
	public function actionStudent(){
		if(isset($_POST["Students"], $_POST["StudentEnrollment"])){
			$save = true;
			foreach($_POST["Students"] as $student){
				$enrollment = new StudentEnrollment();
				$enrollment->attributes = $_POST["StudentEnrollment"];
				
				$st = StudentIdentification::model()->findByPk($student);
				$c = Classroom::model()->findByPk($enrollment->classroom_fk);

				$enrollment->school_inep_id_fk = Yii::app()->user->school;
				$enrollment->student_fk = $st->id;
				$enrollment->student_inep_id = $st->inep_id;
                $enrollment->classroom_inep_id = $c->inep_id;
				$save = $save && $enrollment->save();
			}
			if($save)
				Yii::app()->user->setFlash('success', Yii::t('default', 'Alunos matriculados com sucesso!'));
			else
				Yii::app()->user->setFlash('error', Yii::t('default', 'Erro na matrícula dos Alunos.'));
			$this->render('index');
		}else{
			$this->render('students', array(
				'title' => Yii::t('app', 'Student Configurarion')
			));
		}
	}
	
	public function actionGetStudents(){
		if(isset($_POST["Classrooms"]) && !empty($_POST["Classrooms"])){
			$id = $_POST["Classrooms"];
			$criteria = new CDbCriteria();
			$criteria->join = "JOIN student_enrollment AS se ON (se.student_fk = t.id AND se.classroom_fk = $id)";
			$criteria->order = "name ASC";
				
			$data = CHtml::listData(StudentIdentification::model()->findAll($criteria), 'id', 'name'.'id');
	
			foreach ($data as $value => $name) {
				echo CHtml::tag('option', array('value' => $value), CHtml::encode($name), true);
			}
		}else{
			echo "";
		}
		
	}
	

}