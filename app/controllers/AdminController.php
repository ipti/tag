<?php
	class AdminController extends Controller {
		public $layout = 'fullmenu';
		public function accessRules() {
			return [
				[
					'allow', // allow authenticated user to perform 'create' and 'update' actions
					'actions' => ['CreateUser', 'index', 'conflicts'], 'users' => ['*'],
				], [
					'allow', // allow authenticated user to perform 'create' and 'update' actions
					'actions' => [
						'import', 'export', 'clearDB', 'acl', 'backup', 'data', 'exportStudentIdentify', 'syncExport',
						'syncImport', 'exportToMaster', 'clearMaster', 'importFromMaster'
					], 'users' => ['@'],
				],
			];
		}
		/**
		 * Show the Index Page.
		 */
		public function actionIndex() {
			$this->render('index');
		}
		public function actionCreateUser() {
			$model = new Users;

			if (isset($_POST['Users'], $_POST['Confirm'])) {
				$model->attributes = $_POST['Users'];
				if ($model->validate()) {
					$password = md5($_POST['Users']['password']);
					$confirm = md5($_POST['Confirm']);
					if ($password == $confirm) {
						$model->password = $password;
						// form inputs are valid, do something here
						if ($model->save()) {
							$save = TRUE;
							foreach ($_POST['schools'] as $school) {
								$userSchool = new UsersSchool;
								$userSchool->user_fk = $model->id;
								$userSchool->school_fk = $school;
								$save = $save && $userSchool->validate() && $userSchool->save();
							}
							if ($save) {
								$auth = Yii::app()->authManager;
								$auth->assign($_POST['Role'], $model->id);
								Yii::app()->user->setFlash('success', Yii::t('default', 'Usuário cadastrado com sucesso!'));
								$this->redirect(['index']);
							}
						}
					} else {
						$model->addError('password', Yii::t('default', 'Confirm Password') . ': ' . Yii::t('help', 'Confirm'));
					}
				}
			}
			$this->render('createUser', ['model' => $model]);
		}

		public function actionEditPassword($id) {
			$model = Users::model()->findByPk($id);

			if (isset($_POST['Users'], $_POST['Confirm'])) {
				$password = md5($_POST['Users']['password']);
				$confirm = md5($_POST['Confirm']);
				if ($password == $confirm) {
					$model->password = $password;
					if ($model->save()) {
						Yii::app()->user->setFlash('success', Yii::t('default', 'Senha alterada com sucesso!'));
						$this->redirect(['index']);
					}
				} else {
					$model->addError('password', Yii::t('default', 'Confirm Password') . ': ' . Yii::t('help', 'Confirm'));
				}
			}
			$this->render('editPassword', ['model' => $model]);
		}


		public function actionClearDB() {
			//delete from users_school;
			//delete from users;
			// delete from auth_assignment;

		$command = "
			SET FOREIGN_KEY_CHECKS=0;
			
			delete from auth_assignment;
			delete from users;
			delete from users_school;
			
			delete from class_board;
            delete from class_faults;
            delete from class;

            delete from student_enrollment;
            delete from student_identification;
            delete from student_documents_and_address;

            delete from instructor_teaching_data;
            delete from instructor_identification;
            delete from instructor_documents_and_address;
            delete from instructor_variable_data;

            delete from classroom;

            delete from school_identification;
            delete from school_structure;";

			set_time_limit(0);
			ignore_user_abort();
			Yii::app()->db->createCommand($command)->query();

			$this->addTestUsers();

        Yii::app()->user->setFlash('success', Yii::t('default', 'Banco limpado com sucesso. <br/>Faça o login novamente para atualizar os dados.'));
        $this->redirect(array('index'));
    }
        public function addTestUsers() {
            set_time_limit(0);
            ignore_user_abort();
            $admin_login = 'admin';
            $admin_password = md5('p@s4ipti');

            $command = "INSERT INTO `users`VALUES
                        (1, 'Administrador', '$admin_login', '$admin_password', 1);";
            Yii::app()->db->createCommand($command)->query();

            $auth = Yii::app()->authManager;
            $auth->assign('admin', 1);

//        //Criar usuário de teste, remover depois.
//        /*         * ************************************************************************************************ */
//        /**/$command = "INSERT INTO `users`VALUES"
//                /**/ . "(2, 'Paulo Roberto', 'paulones', 'e10adc3949ba59abbe56e057f20f883e', 1);"
//                /**/ . "INSERT INTO `users_school` (`id`, `school_fk`, `user_fk`) VALUES (1, '28025911', 2);"
//                /**/ . "INSERT INTO `users_school` (`id`, `school_fk`, `user_fk`) VALUES (2, '28025970', 2);"
//                /**/ . "INSERT INTO `users_school` (`id`, `school_fk`, `user_fk`) VALUES (3, '28025989', 2);"
//                /**/ . "INSERT INTO `users_school` (`id`, `school_fk`, `user_fk`) VALUES (4, '28026012', 2);";
//        /**/Yii::app()->db->createCommand($command)->query();
//        /*         * ************************************************************************************************ */
        }
		public function mres($value)
		{
			$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
			$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

			return str_replace($search, $replace, $value);
		}
		public function actionImportMaster(){
			set_time_limit(0);
			ini_set('memory_limit', '-1');
			ignore_user_abort();
			$time1 = time();
			$path = Yii::app()->basePath;
			$uploadfile = $path . '/import/28031610.json';
			$fileDir = $uploadfile;
			$mode = 'r';

			$fileImport = fopen($fileDir, $mode);
			if ($fileImport == FALSE) {
				die('O arquivo não existe.');
			}

		$jsonSyncTag = "";
		while (!feof($fileImport)) {
			$linha = fgets($fileImport, filesize($uploadfile));
			$jsonSyncTag .= $linha;
		}
		fclose($fileImport);
		$json = unserialize($jsonSyncTag);
		$this->loadMaster($json);

	}
	public function loadMaster($loads){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		ignore_user_abort();
		foreach ($loads['schools'] as $index => $scholl) {
			$saveschool = new SchoolIdentification();
			$saveschool->setDb2Connection(true);
			$saveschool->refreshMetaData();
			$exist = $saveschool->findByAttributes(array('inep_id'=>$scholl['inep_id']));
			if(!isset($exist)){
				$saveschool->attributes = $scholl;
				$saveschool->save();
			}
		}
		foreach ($loads['schools_structure'] as $index => $structure) {
			$saveschool = new SchoolStructure();
			$saveschool->setDb2Connection(true);
			$saveschool->refreshMetaData();
			$exist = $saveschool->findByAttributes(array('school_inep_id_fk'=>$structure['school_inep_id_fk']));
			if(!isset($exist)){
				$saveschool->attributes = $structure;
				$saveschool->save();
			}
		}
		foreach ($loads['classrooms'] as $index => $class) {
			$saveclass = new Classroom();
			$saveclass->setScenario('search');
			$saveclass->setDb2Connection(true);
			$saveclass->refreshMetaData();
			$exist = $saveclass->findByAttributes(array('hash'=>$class['hash']));
			if (!isset($exist)){
				$saveclass->attributes = $class;
				$saveclass->hash = $class['hash'];
				$saveclass->save();
			}

			//else{
			//$exist->attributes = $class;
			//$exist->save()
			//}
		}
		foreach ($loads['students'] as $i => $student) {
			$savestudent = new StudentIdentification();
			$savestudent->setScenario('search');
			$savestudent->setDb2Connection(true);
			$savestudent->refreshMetaData();
			$exist = $savestudent->findByAttributes(array('hash'=>$student['hash']));
			if (!isset($exist)){
				$savestudent->attributes = $student;
				$savestudent->hash = $student['hash'];
				$savestudent->save();
			}
		}

		foreach ($loads['documentsaddress'] as $i => $documentsaddress) {
			$savedocument = new StudentDocumentsAndAddress();
			$savedocument->setScenario('search');
			$savedocument->setDb2Connection(true);
			$savedocument->refreshMetaData();
			$exist = $savedocument->findByAttributes(array('hash'=>$documentsaddress['hash']));
			if (!isset($exist)){
				$savedocument->attributes = $documentsaddress;
				$savedocument->hash = $documentsaddress['hash'];
				$savedocument->save();
			}
		}

		foreach ($loads['enrollments'] as $index => $enrollment) {
			$saveenrollment = new StudentEnrollment();
			$saveenrollment->setScenario('search');
			$saveenrollment->setDb2Connection(true);
			$saveenrollment->refreshMetaData();
			$exist = $saveenrollment->findByAttributes(array('hash'=>$enrollment['hash']));
			if (!isset($exist)){
				$saveenrollment->attributes = $enrollment;
				$saveenrollment->hash = $enrollment['hash'];
				$saveenrollment->hash_classroom = $enrollment['hash_classroom'];
				$saveenrollment->hash_student = $enrollment['hash_student'];
				$saveenrollment->validate();
				$saveenrollment->save();
				if(!empty($saveenrollment->errors)){
				}
			}
		}
		//@TODO FAZER A PARTE DE PROFESSORES A PARTIR DAQUI

	}
	public function prepareExport(){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		ignore_user_abort();
		$year = Yii::app()->user->year;
		/*$sql = "SELECT DISTINCT(school_inep_id_fk) FROM student_enrollment a
                JOIN classroom b ON(a.`classroom_fk`=b.id)
                WHERE
                b.`school_year`=$year";*/
		$sql = "SELECT inep_id as school_inep_id_fk  FROM school_identification where situation='1'";
		$schools = Yii::app()->db->createCommand($sql)->queryAll();
		$istudent = new StudentIdentification();
		$istudent->setDb2Connection(false);
		$istudent->refreshMetaData();
		$studentAll = $istudent->findAll();
		try {
			Yii::app()->db2;
			$conn = true;
		}
		catch(Exception $ex) {
			$conn = false;
		}
		if($conn){
			foreach ($studentAll as $index => $student) {
				$hash_student = hexdec(crc32($student->name.$student->birthday));
				if(!isset($loads['students'][$hash_student])){
					$loads['students'][$hash_student] = $student->attributes;
					$loads['students'][$hash_student]['hash'] = $hash_student;
				}
				if(!isset($loads['documentsaddress'][$hash_student])){
					$idocs = new StudentDocumentsAndAddress();
					$idocs->setDb2Connection(false);
					$idocs->refreshMetaData();
					$loads['documentsaddress'][$hash_student] = $idocs->findByPk($student->id)->attributes;
					$loads['documentsaddress'][$hash_student]['hash'] = $hash_student;
				}
			}
		}
		foreach ($schools as $index => $schll) {
			$ischool = new SchoolIdentification();
			$ischool->setDb2Connection(false);
			$ischool->refreshMetaData();
			$school = $ischool->findByPk($schll['school_inep_id_fk']);

			$iclass = new Classroom();
			$iclass->setDb2Connection(false);
			$iclass->refreshMetaData();
			$classrooms = $iclass->findAllByAttributes(["school_inep_fk" => $schll['school_inep_id_fk'], "school_year" => Yii::app()->user->year]);
			$hash_school = hexdec(crc32($school->inep_id.$school->name));
			$loads['schools'][$hash_school] = $school->attributes;
			$loads['schools'][$hash_school]['hash'] = $hash_school;
			//@todo adicionado load na tabela de schoolstructure
			$loads['schools_structure'][$hash_school] = $school->structure->attributes;
			$loads['schools_structure'][$hash_school]['hash'] = $hash_school;
			foreach ($classrooms as $iclass => $classroom) {
				$hash_classroom = hexdec(crc32($school->inep_id.$classroom->id.$classroom->school_year));
				$loads['classrooms'][$hash_classroom] = $classroom->attributes;
				$loads['classrooms'][$hash_classroom]['hash'] = $hash_classroom;
				foreach ($classroom->studentEnrollments as $ienrollment => $enrollment) {
					$enrollment->setDb2Connection(false);
					$enrollment->refreshMetaData();
					$hash_student = hexdec(crc32($enrollment->studentFk->name.$enrollment->studentFk->birthday));
					if(!isset($loads['students'][$hash_student])){
						$loads['students'][$hash_student] = $enrollment->studentFk->attributes;
						$loads['students'][$hash_student]['hash'] = $hash_student;
					}
					if(!isset($loads['documentsaddress'][$hash_student])){
						$loads['documentsaddress'][$hash_student] = $enrollment->studentFk->documentsFk->attributes;
						$loads['documentsaddress'][$hash_student]['hash'] = $hash_student;
					}
					$hash_enrollment = hexdec(crc32($hash_classroom.$hash_student));
					$loads['enrollments'][$hash_enrollment] = $enrollment->attributes;
					$loads['enrollments'][$hash_enrollment]['hash'] = $hash_enrollment;
					$loads['enrollments'][$hash_enrollment]['hash_classroom'] = $hash_classroom;
					$loads['enrollments'][$hash_enrollment]['hash_student'] = $hash_student;
				}
				/*
                foreach ($classroom->instructorTeachingDatas as $iteaching => $teachingData) {
                    //CARREGAR AS INFORMAÇÕES DE TEACHING DATA;
                    $hash_instructor = hexdec(crc32($teachingData->instructorFk->name.$teachingData->instructorFk->birthday_date));
                    $hash_teachingdata = hexdec(crc32($hash_classroom.$hash_instructor));
                    $loads['instructorsteachingdata'][$teachingData->instructor_fk][$classroom->id] = $teachingData->attributes;
                    $loads['instructorsteachingdata'][$teachingData->instructor_fk][$classroom->id]['hash_instructor'] = $hash_instructor;
                    $loads['instructorsteachingdata'][$teachingData->instructor_fk][$classroom->id]['hash_classroom'] = $hash_classroom;
                    $loads['instructorsteachingdata'][$teachingData->instructor_fk][$classroom->id]['hash'] = $hash_teachingdata;

                    //CARREGAR AS INFORMAÇÕES DE TEACHING DATA;
                    if(!isset($loads['instructors'][$teachingData->instructor_fk])){
                        $loads['instructors'][$teachingData->instructor_fk]['identification'] = $teachingData->instructorFk->attributes;
                        $loads['instructors'][$teachingData->instructor_fk]['identification']['hash'] = $hash_instructor;
                        $loads['idocuments'][$teachingData->instructor_fk]['documents'] = $teachingData->instructorFk->documents->attributes;
                        $loads['idocuments'][$teachingData->instructor_fk]['documents']['hash'] = $hash_instructor;

                    }
                    if(!isset($loads['instructorsvariabledata'][$teachingData->instructor_fk])) {
                        $loads['instructorsvariabledata'][$teachingData->instructor_fk] = $teachingData->instructorFk->instructorVariableData->attributes;
                        $loads['instructorsvariabledata'][$teachingData->instructor_fk]['hash'] = $hash_instructor;
                    }
                }*/

			}
		}
		return $loads;



	}
	public function actionExportMaster(){
		try {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', '-1');
			set_time_limit(0);
			ignore_user_abort();
			Yii::app()->db2;
			$sql = "SELECT DISTINCT `TABLE_SCHEMA` FROM `information_schema`.`TABLES` WHERE TABLE_SCHEMA LIKE 'io.escola.%';";
			$dbs = Yii::app()->db2->createCommand($sql)->queryAll();
			$loads = array();
			$priority['TABLE_SCHEMA'] = Yii::app()->db->createCommand("SELECT DATABASE()")->queryScalar();
			array_unshift($dbs, $priority);
			foreach ($dbs as $db) {
				if($db['TABLE_SCHEMA'] != 'io.escola.demo' && $db['TABLE_SCHEMA'] != 'io.escola.geminiano'){
					$dbname = $db['TABLE_SCHEMA'];
					Yii::app()->db->setActive(false);
					Yii::app()->db->connectionString = "mysql:host=localhost;dbname=$dbname";
					Yii::app()->db->setActive(true);
					$add = $this->prepareExport();
					$this->loadMaster($add);
				}
				
			}
			Yii::app()->user->setFlash('success', Yii::t('default', 'Escola exportada com sucesso!'));
			$this->redirect(['index']);
		} catch (Exception $e) {
			$loads = $this->prepareExport();
			$datajson = serialize($loads);
			ini_set('memory_limit', '288M');
			$fileName = "./app/export/" . Yii::app()->user->school . ".json";
			$file = fopen($fileName, "w");
			fwrite($file, $datajson);
			fclose($file);
			header("Content-Disposition: attachment; filename=\"" . basename($fileName) . "\"");
			header("Content-Type: application/force-download");
			header("Content-Length: " . filesize($fileName));
			header("Connection: close");
			$file = fopen($fileName, "r");
			fpassthru($file);
			fclose($file);
			unlink($fileName);
		}
	}
}

?>
