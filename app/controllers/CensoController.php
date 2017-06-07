<?php

	//@done S2 - Modularizar o código do import
	//@done S2 - Criar o controller de Import
	//@done S2 - Mover o código do import de SchoolController.php para AdminController.php
	//@done S2 - Mover o código do configACL de SchoolController.php para AdminController.php
	//@done S2 - Criar método de limparBanco
	//@done S2 - Criar tela de index do AdminController.php
	//@done S2 - Criar usuários padrões.
	//@done S2 - Mensagens de retorno ao executar os scripts.


	class CensoController extends Controller {

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
		public function areThereByModalitie($sql){
			$people_by_modalitie = Yii::app()->db->createCommand($sql)->queryAll();
			$modalities_regular	= false;
			$modalities_especial = false;
			$modalities_eja = false;
			$modalities_professional = false;
			foreach ($people_by_modalitie as $key => $item) {
				switch ($item['modalities']) {

					case '1':
						if($item['number_of'] > '0')
							$modalities_regular = true;
						break;
					case '2':
						if($item['number_of'] > '0')
							$modalities_especial = true;
						break;

					case '3':
						if($item['number_of'] > '0')
							$modalities_eja = true;
						break;

					case '4':
						if($item['number_of'] > '0')
							$modalities_professional = true;
						break;
				}
			}
			return array("modalities_regular" => $modalities_regular,
				"modalities_especial" => $modalities_especial,
				"modalities_eja" => $modalities_eja,
				"modalities_professional" => $modalities_professional);
		}
		public function validateSchool($collumn){
			$log = array();
			$siv = new SchoolIdentificationValidation();

			//ok
			$result = $siv->isRegister("00", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//ok
			$result = $siv->isInepIdValid($collumn['inep_id']);
			if(!$result["status"]) array_push($log, array("inep_id"=>$result["erro"]));
			
			//campo 3 - ok
			$result = $siv->isManagerCPFValid($collumn['manager_cpf']);
			if(!$result["status"]) array_push($log, array("manager_cpf"=>$result["erro"]));

			//campo 4 - ok
			$result = $siv->isManagerNameValid($collumn['manager_name']);
			if(!$result["status"]) array_push($log, array("manager_name"=>$result["erro"]));

			//campo 5 -ok
			$result = $siv->isManagerRoleValid(intval($collumn['manager_role']));
			if(!$result["status"]) array_push($log, array("manager_role"=>$result["erro"]));

			//campo 6 - ok
			$result = $siv->isManagerEmailValid($collumn['manager_email']);
			if(!$result["status"]) array_push($log, array("manager_email"=>$result["erro"]));

			//campo 7 - ok
			$result = $siv->isSituationValid($collumn['situation']);
			if(!$result["status"]) array_push($log, array("situation"=>$result["erro"]));

			//campo 8 e 9
			/*@todo
			1. Deve ser preenchido quando o campo 7 (Situação de funcionamento) for igual a 1 (em atividade).
			2. Deve ser nulo quando o campo 7 (Situação de funcionamento) for diferente de 1 (em atividade).

			*/
			$result = $siv->isSchoolYearValid($collumn['initial_date'], $collumn['final_date']);
			if(!$result["status"]) array_push($log, array("date"=>$result["erro"]));

			//campo 10
			$result = $siv->isSchoolNameValid($collumn['name']);
			if(!$result["status"]) array_push($log, array("name"=>$result["erro"]));

			//campo 11
			$result = $siv->isLatitudeValid($collumn['latitude']);
			if(!$result["status"]) array_push($log, array("latitude"=>$result["erro"]));

			//campo 12
			$result = $siv->isLongitudeValid($collumn['longitude']);
			if(!$result["status"]) array_push($log, array("longitude"=>$result["erro"]));

			//campo 13
			$result = $siv->isCEPValid($collumn['cep']);
			if(!$result["status"]) array_push($log, array("cep"=>$result["erro"]));

			//campo 14
			$might_be_null = true;
			$might_not_be_null = false;


			$result = $siv->isAddressValid($collumn['address'], $might_not_be_null, 100);
			if(!$result["status"]) array_push($log, array("address"=>$result["erro"]));

			//campo 15
			$result = $siv->isAddressValid($collumn['address_number'], $might_be_null, 10);
			if(!$result["status"]) array_push($log, array("address_number"=>$result["erro"]));

			//campo 16
			$result = $siv->isAddressValid($collumn['address_complement'], $might_be_null, 20);
			if(!$result["status"]) array_push($log, array("address_complement"=>$result["erro"]));;

			//campo 17
			$result = $siv->isAddressValid($collumn['address_neighborhood'], $might_be_null, 50);
			if(!$result["status"]) array_push($log, array("address_neighborhood"=>$result["erro"]));

			//campo 18
			/* @todo
			 * 1. Deve ser preenchido.
				2. Deve ser preenchido com o código do estado de acordo com a “Tabela de UF”.
				2. O valor preenchido deve ser o mesmo que está na base do Educacenso.
			 * $result = $siv->isAddressValid($collumn['edcenso_uf_fk'], $might_not_be_null, 100);
			if(!$result["status"]) array_push($log, array("edcenso_uf_fk"=>$result["erro"]));

			//campo 19
			$result = $siv->isAddressValid($collumn['edcenso_city_fk'], $might_not_be_null, 100);
			if(!$result["status"]) array_push($log, array("edcenso_city_fk"=>$result["erro"]));

			//campo 20
			$result = $siv->isAddressValid($collumn['edcenso_district_fk'], $might_not_be_null, 100);
			if(!$result["status"]) array_push($log, array("edcenso_district_fk"=>$result["erro"]));
			 *
			 * */
			//campo 21-25

			$phones = array($collumn['phone_number'],
				$collumn['public_phone_number'],
				$collumn['other_phone_number'],
				$collumn['fax_number']);
			$result = $siv->checkPhoneNumbers($collumn['ddd'], $phones);
			if(!$result["status"]) array_push($log, array("phones"=>$result["erro"]));

			//campo 26
			$result = $siv->isEmailValid($collumn['email']);
			if(!$result["status"]) array_push($log, array("email"=>$result["erro"]));

			//campo 27


			//campo 28
			$result = $siv->isAdministrativeDependenceValid($collumn['administrative_dependence'], $collumn['edcenso_uf_fk']);
			if(!$result["status"]) array_push($log, array("administrative_dependence"=>$result["erro"]));

			//campo 29
			$result = $siv->isLocationValid($collumn['location']);
			if(!$result["status"]) array_push($log, array("location"=>$result["erro"]));

			//campo 30
			$result = $siv->checkPrivateSchoolCategory($collumn['private_school_category'],
				$collumn['situation'],
				$collumn['administrative_dependence']);
			if(!$result["status"]) array_push($log, array("private_school_category"=>$result["erro"]));

			//campo 31
			$result = $siv->isPublicContractValid($collumn['public_contract'],
				$collumn['situation'],
				$collumn['administrative_dependence']);
			if(!$result["status"]) array_push($log, array("public_contract"=>$result["erro"]));

			//campo 32 - 36
			$keepers = array($collumn['private_school_business_or_individual'],
				$collumn['private_school_syndicate_or_association'],
				$collumn['private_school_ong_or_oscip'],
				$collumn['private_school_non_profit_institutions'],
				$collumn['private_school_s_system']);

			/*
			$result = $siv->checkPrivateSchoolCategory($keepers,
				$collumn['situation'],
				$collumn['administrative_dependence']);
			if(!$result["status"]) array_push($log, array("keepers"=>$result["erro"]));
			*/

			//campo 37
			$result = $siv->isCNPJValid($collumn['private_school_maintainer_cnpj'],
				$collumn['situation'],
				$collumn['administrative_dependence']);
			if(!$result["status"]) array_push($log, array("private_school_maintainer_cnpj"=>$result["erro"]));

			//campo 38
			$result = $siv->isCNPJValid($collumn['private_school_cnpj'],
				$collumn['situation'],
				$collumn['administrative_dependence']);
			if(!$result["status"]) array_push($log, array("private_school_cnpj"=>$result["erro"]));

			//campo 39
			$result = $siv->isRegulationValid($collumn['regulation'],
				$collumn['situation']);
			if(!$result["status"]) array_push($log, array("regulation"=>$result["erro"]));

			//campo 40
			$result = $siv->isRegulationValid($collumn['offer_or_linked_unity'],
				$collumn['situation']);
			if(!$result["status"]) array_push($log, array("offer_or_linked_unity"=>$result["erro"]));

			//campo 41
			$inep_head_school = $collumn['inep_head_school'];
			$sql = "SELECT 	si.inep_head_school, si.situation
			FROM 	school_identification AS si
			WHERE 	inep_id = '$inep_head_school';";
			$check =  Yii::app()->db->createCommand($sql)->queryAll();
			if(!empty($check)){
				$result = $siv->inepHeadSchool($collumn['inep_head_school'], $collumn['offer_or_linked_unity'],
					$collumn['inep_id'], $check[0]['situation'],
					$check[0]['inep_head_school']);
				if(!$result["status"]) array_push($log, array("inep_head_school"=>$result["erro"]));
			}
			//campo 42
			$ies_code = $collumn['ies_code'];
			$administrative_dependence = $collumn['administrative_dependence'];
			$sql = "SELECT 	COUNT(id) AS status
			FROM 	edcenso_ies
			WHERE 	id = '$ies_code' AND working_status = 'ATIVA'
					AND administrative_dependency_code = '$administrative_dependence';";
			$check =  Yii::app()->db->createCommand($sql)->queryAll();

			$result = $siv->iesCode($ies_code, $check[0]["status"],$collumn['offer_or_linked_unity']);
			if(!$result["status"]) array_push($log, array("ies_code"=>$result["erro"]));

			//Adicionando log da row
			return $log;
		}
		public function validateSchoolStructure($collumn,$school){
			$ssv = new SchoolStructureValidation();
			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$log = array();

			//campo 1
			$result = $ssv->isRegister("10", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//campo 2
			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}

			$result = $ssv->isAllowed($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 3 à 11
			$operation_locations = array($collumn["operation_location_building"],
				$collumn["operation_location_temple"],
				$collumn["operation_location_businness_room"],
				$collumn["operation_location_instructor_house"],
				$collumn["operation_location_other_school_room"],
				$collumn["operation_location_barracks"],
				$collumn["operation_location_socioeducative_unity"],
				$collumn["operation_location_prison_unity"],
				$collumn["operation_location_other"]);
			$result = $ssv->atLeastOne($operation_locations);
			if(!$result["status"]) array_push($log, array("operation_locations"=>$result["erro"]));

			//campo 12
			$result = $ssv->buildingOccupationStatus($collumn["operation_location_building"],
				$collumn["operation_location_barracks"],
				$collumn["building_occupation_situation"]);
			if(!$result["status"]) array_push($log, array("building_occupation_situation"=>$result["erro"]));

			//campo 13
			$result = $ssv->sharedBuildingSchool($collumn["operation_location_building"],
				$collumn["shared_building_with_school"]);
			if(!$result["status"]) array_push($log, array("shared_building_with_school"=>$result["erro"]));

			//campos 14 à 19
			$shared_school_inep_ids = array($collumn["shared_school_inep_id_1"],
				$collumn["shared_school_inep_id_2"],
				$collumn["shared_school_inep_id_3"],
				$collumn["shared_school_inep_id_4"],
				$collumn["shared_school_inep_id_5"],
				$collumn["shared_school_inep_id_6"]);
			$result = $ssv->sharedSchoolInep($collumn["shared_building_with_school"],
				$collumn["school_inep_id_fk"],
				$shared_school_inep_ids);
			if(!$result["status"]) array_push($log, array("shared_school_inep_ids"=>$result["erro"]));

			//campo 20
			$result = $ssv->oneOfTheValues($collumn["consumed_water_type"]);
			if(!$result["status"]) array_push($log, array("consumed_water_type"=>$result["erro"]));

			//campos 21 à 25
			$water_supplys = array($collumn["water_supply_public"],
				$collumn["water_supply_artesian_well"],
				$collumn["water_supply_well"],
				$collumn["water_supply_river"],
				$collumn["water_supply_inexistent"]);
			$result = $ssv->supply($water_supplys);
			if(!$result["status"]) array_push($log, array("water_supplys"=>$result["erro"]));

			//campos 26 à 29
			$energy_supplys = array($collumn["energy_supply_public"],
				$collumn["energy_supply_generator"],
				$collumn["energy_supply_other"],
				$collumn["energy_supply_inexistent"]);
			$result = $ssv->supply($energy_supplys);
			if(!$result["status"]) array_push($log, array("energy_supplys"=>$result["erro"]));

			//campos 30 à 32
			$sewages = array($collumn["sewage_public"],
				$collumn["sewage_fossa"],
				$collumn["sewage_inexistent"]);
			$result = $ssv->supply($sewages);
			if(!$result["status"]) array_push($log, array("sewages"=>$result["erro"]));

			//campos 33 à 38
			$garbage_destinations = array($collumn["garbage_destination_collect"],
				$collumn["garbage_destination_burn"],
				$collumn["garbage_destination_throw_away"],
				$collumn["garbage_destination_recycle"],
				$collumn["garbage_destination_bury"],
				$collumn["garbage_destination_other"]);
			$result = $ssv->atLeastOne($garbage_destinations);
			if(!$result["status"]) array_push($log, array("garbage_destinations"=>$result["erro"]));

			//campos 39 à 68
			$dependencies = array($collumn["dependencies_principal_room"],
				$collumn["dependencies_instructors_room"],
				$collumn["dependencies_secretary_room"],
				$collumn["dependencies_info_lab"],
				$collumn["dependencies_science_lab"],
				$collumn["dependencies_aee_room"],
				$collumn["dependencies_indoor_sports_court"],
				$collumn["dependencies_outdoor_sports_court"],
				$collumn["dependencies_kitchen"],
				$collumn["dependencies_library"],
				$collumn["dependencies_reading_room"],
				$collumn["dependencies_playground"],
				$collumn["dependencies_nursery"],
				$collumn["dependencies_outside_bathroom"],
				$collumn["dependencies_inside_bathroom"],
				$collumn["dependencies_child_bathroom"],
				$collumn["dependencies_prysical_disability_bathroom"],
				$collumn["dependencies_physical_disability_support"],
				$collumn["dependencies_bathroom_with_shower"],
				$collumn["dependencies_refectory"],
				$collumn["dependencies_storeroom"],
				$collumn["dependencies_warehouse"],
				$collumn["dependencies_auditorium"],
				$collumn["dependencies_covered_patio"],
				$collumn["dependencies_uncovered_patio"],
				$collumn["dependencies_student_accomodation"],
				$collumn["dependencies_instructor_accomodation"],
				$collumn["dependencies_green_area"],
				$collumn["dependencies_laundry"],
				$collumn["dependencies_none"]);
			$result = $ssv->supply($dependencies);
			if(!$result["status"]) array_push($log, array("dependencies"=>$result["erro"]));

			//campo 69
			$result = $ssv->schoolsCount($collumn["operation_location_building"],
				$collumn["classroom_count"]);
			if(!$result["status"]) array_push($log, array("classroom_count"=>$result["erro"]));

			//campo 70
			$result = $ssv->isGreaterThan($collumn["used_classroom_count"], "0");
			if(!$result["status"]) array_push($log, array("used_classroom_count"=>$result["erro"]));

			//campo 71 à 83
			$result = $ssv->isGreaterThan($collumn["used_classroom_count"], "0");
			if(!$result["status"]) array_push($log, array("used_classroom_count"=>$result["erro"]));

			//campo 84
			$result = $ssv->pcCount($collumn["equipments_computer"],
				$collumn["administrative_computers_count"]);
			if(!$result["status"]) array_push($log, array("administrative_computers_count"=>$result["erro"]));

			//campo 85
			$result = $ssv->pcCount($collumn["equipments_computer"],
				$collumn["student_computers_count"]);
			if(!$result["status"]) array_push($log, array("student_computers_count"=>$result["erro"]));

			//campo 86
			$result = $ssv->internetAccess($collumn["equipments_computer"],
				$collumn["internet_access"]);
			if(!$result["status"]) array_push($log, array("internet_access"=>$result["erro"]));

			//campo 87
			$result = $ssv->bandwidth($collumn["internet_access"],
				$collumn["bandwidth"]);
			if(!$result["status"]) array_push($log, array("bandwidth"=>$result["erro"]));

			//campo 88
			$result = $ssv->isGreaterThan($collumn["employees_count"], "0");
			if(!$result["status"]) array_push($log, array("employees_count"=>$result["erro"]));

			$school_inep_fk = $school['inep_id'];
			//campo 89
			$sql = 'SELECT  COUNT(pedagogical_mediation_type) AS number_of
		FROM 	classroom
		WHERE 	school_inep_fk = "$school_inep_id_fk" AND
				(pedagogical_mediation_type =  "1" OR pedagogical_mediation_type =  "2");';
			$pedagogical_mediation_type = Yii::app()->db->createCommand($sql)->queryAll();


			$result = $ssv->schoolFeeding($school["administrative_dependence"],
				$collumn["feeding"],
				$pedagogical_mediation_type[0]["number_of"]);
			if(!$result["status"]) array_push($log, array("feeding"=>$result["erro"]));

			//campo 90
			$sql = "SELECT 	COUNT(assistance_type) AS number_of
			FROM 	classroom
			WHERE 	assistance_type = '5' AND
					school_inep_fk = '$school_inep_fk';" ;
			$assistance_type = Yii::app()->db->createCommand($sql)->queryAll();


			$modalities = array("modalities_regular" => $collumn["modalities_regular"],
				"modalities_especial" => $collumn["modalities_especial"],
				"modalities_eja" =>	$collumn["modalities_eja"],
				"modalities_professional" => $collumn["modalities_professional"]);

			$result = $ssv->aee($collumn["aee"], $collumn["complementary_activities"], $modalities,
				$assistance_type[0]["number_of"]);
			if(!$result["status"]) array_push($log, array("aee"=>$result["erro"]));

			//campo 91 - colocar o ano
			$sql = "SELECT 	COUNT(assistance_type) AS number_of
			FROM 	classroom
			WHERE 	assistance_type = '4' AND
					school_inep_fk = '$school_inep_fk';" ;
			$assistance_type = Yii::app()->db->createCommand($sql)->queryAll();


			$result = $ssv->aee($collumn["complementary_activities"], $collumn["aee"], $modalities,
				$assistance_type[0]["number_of"]);
			if(!$result["status"]) array_push($log, array("complementary_activities"=>$result["erro"]));

			//campo 92 à 95
			$year = Yii::app()->user->year;
			$sql = "SELECT  modalities, COUNT(se.student_fk) as number_of
			FROM	edcenso_stage_vs_modality_complementary as esmc
						INNER JOIN
					classroom AS cr
						ON esmc.fk_edcenso_stage_vs_modality = cr.edcenso_stage_vs_modality_fk
						INNER JOIN
					student_enrollment AS se
						ON cr.id = se.classroom_fk
			WHERE cr.school_year = '$year'
			GROUP BY esmc.modalities;";
			$are_there_students_by_modalitie = $this->areThereByModalitie($sql);
			$sql = "SELECT  modalities, COUNT(itd.instructor_fk) as number_of
		FROM	edcenso_stage_vs_modality_complementary as esmc
					INNER JOIN
				classroom AS cr
					ON esmc.fk_edcenso_stage_vs_modality = cr.edcenso_stage_vs_modality_fk
					INNER JOIN
				instructor_teaching_data AS itd
					ON cr.id = itd.classroom_id_fk
		WHERE cr.school_year = '$year'
		GROUP BY esmc.modalities;";
			$are_there_instructors_by_modalitie = $this->areThereByModalitie($sql);
			


			$result = $ssv->checkModalities($collumn["aee"],
				$collumn["complementary_activities"],
				$modalities,
				$are_there_students_by_modalitie,
				$are_there_instructors_by_modalitie);
			if(!$result["status"]) array_push($log, array("modalities"=>$result["erro"]));

			//campo 96
			$sql = "SELECT 	DISTINCT  COUNT(esm.id) AS number_of, cr.school_inep_fk
			FROM 	classroom AS cr
						INNER JOIN
					edcenso_stage_vs_modality AS esm
						ON esm.id = cr.edcenso_stage_vs_modality_fk
			WHERE 	stage IN (2,3,7) AND cr.school_inep_fk = '$school_inep_fk';";
			$number_of_schools = Yii::app()->db->createCommand($sql)->queryAll();

			$result = $ssv->schoolCicle($collumn["basic_education_cycle_organized"], $number_of_schools);
			if(!$result["status"]) array_push($log, array("basic_education_cycle_organized"=>$result["erro"]));

			//campo 97
			$result = $ssv->differentiatedLocation($school["inep_id"],
				$collumn["different_location"]);
			if(!$result["status"]) array_push($log, array("different_location"=>$result["erro"]));

			//campo 98 à 100
			$sociocultural_didactic_materials = array($collumn["sociocultural_didactic_material_none"],
				$collumn["sociocultural_didactic_material_quilombola"],
				$collumn["sociocultural_didactic_material_native"]);
			$result = $ssv->materials($sociocultural_didactic_materials);
			if(!$result["status"]) array_push($log, array("sociocultural_didactic_materials"=>$result["erro"]));

			//101
			$result = $ssv->isAllowed($collumn["native_education"], array("0", "1"));
			if(!$result["status"]) array_push($log, array("native_education"=>$result["erro"]));

			//102 à 103
			$native_education_languages = array($collumn["native_education_language_native"],
				$collumn["native_education_language_portuguese"]);
			$result = $ssv->languages($collumn["native_education"], $native_education_languages);
			if(!$result["status"]) array_push($log, array("native_education_languages"=>$result["erro"]));

			//104
			$result = $ssv->edcensoNativeLanguages($collumn["native_education_language_native"],
				$collumn["edcenso_native_languages_fk"]);
			if(!$result["status"]) array_push($log, array("edcenso_native_languages_fk"=>$result["erro"]));

			//105
			$result = $ssv->isAllowed($collumn["brazil_literate"], array("0", "1"));
			if(!$result["status"]) array_push($log, array("brazil_literate"=>$result["erro"]));

			//106
			$result = $ssv->isAllowed($collumn["open_weekend"], array("0", "1"));
			if(!$result["status"]) array_push($log, array("open_weekend"=>$result["erro"]));

			//107
			$sql = "SELECT 	COUNT(esm.id ) AS number_of
			FROM 	classroom AS cr
						INNER JOIN
					edcenso_stage_vs_modality AS esm
						ON esm.id = cr.edcenso_stage_vs_modality_fk
			WHERE 	cr.assistance_type NOT IN (4,5) AND
					cr.school_inep_fk =  '$school_inep_id_fk' AND
					esm.stage NOT IN (1,2);";
			$pedagogical_formation_by_alternance = Yii::app()->db->createCommand($sql)->queryAll();

			$result = $ssv->pedagogicalFormation($collumn["pedagogical_formation_by_alternance"],
				$pedagogical_formation_by_alternance[0]["number_of"]);
			if(!$result["status"]) array_push($log, array("pedagogical_formation_by_alternance"=>$result["erro"]));
			return $log;
		}
		public function validateClassroom($column,$school,$schoolstructure){
			$crv = new ClassroomValidation();
			$log = array();

			//campo 1
			$result = $crv->isRegister('20', $column['register_type']);
			if (!$result['status']) array_push($log, array('register_type' => $result['erro']));

			//campo 2
			$result = $crv->isEqual($column['school_inep_fk'], $school['inep_id'], 'Inep ids sao diferentes');
			if (!$result['status']) array_push($log, array('school_inep_fk' => $result['erro']));

			//campo 3
			$result = $crv->isEmpty($column['inep_id']);
			if (!$result['status']) array_push($log, array('inep_id' => $result['erro']));

			//campo 4
			$result = $crv->checkLength($column['id'], 20);
			if (!$result['status']) array_push($log, array('id' => $result['erro']));

			//campo 5
			$result = $crv->isValidClassroomName($column['name']);
			if (!$result['status']) array_push($log, array('name' => $result['erro']));

			//campo 6
			$result = $crv->isValidMediation($column['pedagogical_mediation_type']);
			if (!$result['status']) array_push($log, array('pedagogical_mediation_type' => $result['erro']));

			//campos 7 a 10
			$result = $crv->isValidClassroomTime($column['initial_hour'], $column['initial_minute'],
				$column['final_hour'], $column['final_minute'],
				$column['pedagogical_mediation_type']);
			if (!$result['status']) array_push($log, array('classroom_time' => $result['erro']));
			//acima: imprimir "classroom_time" no erro ou um erro pra cada um dos campos?

			//campos 11 a 17
			$result = $crv->areValidClassroomDays(array($column['week_days_sunday'], $column['week_days_monday'], $column['week_days_tuesday'],
				$column['week_days_wednesday'], $column['week_days_thursday'], $column['week_days_friday'],
				$column['week_days_saturday']), $column['pedagogical_mediation_type']);
			if (!$result['status']) array_push($log, array('classroom_days' => $result['erro']));
			//acima: imprimir "classroom_days" no erro ou um erro pra cada um dos campos?

			//campo 18
			$result = $crv->isValidAssistanceType($schoolstructure, $column['assistance_type'], $column['pedagogical_mediation_type']);
			if (!$result['status']) array_push($log, array('assistance_type' => $result['erro']));

			//campo 19
			$result = $crv->isValidMaisEducacaoParticipator($column['mais_educacao_participator'], $column['pedagogical_mediation_type'], $school['administrative_dependence'],
				$column['assistance_type'], $column['modality'], $column['edcenso_stage_vs_modality_fk']);
			if (!$result['status']) array_push($log, array('mais_educacao_participator' => $result['erro']));

			//campos 20 a 25
			$activities = array($column['complementary_activity_type_1'], $column['complementary_activity_type_2'], $column['complementary_activity_type_3'],
				$column['complementary_activity_type_4'], $column['complementary_activity_type_5'], $column['complementary_activity_type_6']);
			$result = $crv->isValidComplementaryActivityType($activities, $column['assistance_type']);
			if (!$result['status']) array_push($log, array('complementary_activity_types' => $result['erro']));

			//campos 26 a 36
			$aeeArray = array($column['aee_braille_system_education'], $column['aee_optical_and_non_optical_resources'],
				$column['aee_mental_processes_development_strategies'], $column['aee_mobility_and_orientation_techniques'],
				$column['aee_libras'], $column['aee_caa_use_education'], $column['aee_curriculum_enrichment_strategy'],
				$column['aee_soroban_use_education'], $column['aee_usability_and_functionality_of_computer_accessible_education'],
				$column['aee_teaching_of_Portuguese_language_written_modality'], $column['aee_strategy_for_school_environment_autonomy']);
			$result = $crv->isValidAEE($aeeArray, $column['assistance_type']);
			if (!$result['status']) array_push($log, array('aee' => $result['erro']));

			//campo 37

			$schoolStructureModalities['modalities_regular'] = $schoolstructure['modalities_regular'];
			$schoolStructureModalities['modalities_especial'] = $schoolstructure['modalities_especial'];
			$schoolStructureModalities['modalities_eja'] = $schoolstructure['modalities_eja'];
			$schoolStructureModalities['modalities_professional'] = $schoolstructure['modalities_professional'];
			$result = $crv->isValidModality($column['modality'], $column['assistance_type'], $schoolStructureModalities, $column['pedagogical_mediation_type']);
			if (!$result['status']) array_push($log, array('modality' => $result['erro']));

			//campo 38

			//abaixo: $column['stage'] ou $column['edcenso_stage_vs_modality_fk']?
			$result = $crv->isValidStage($column['edcenso_stage_vs_modality_fk'], $column['assistance_type'], $column['pedagogical_mediation_type']);
			if (!$result['status']) array_push($log, array('stage' => $result['erro']));

			//campo 39
			$result = $crv->isValidProfessionalEducation($column['edcenso_professional_education_course_fk'], $column['edcenso_stage_vs_modality_fk']);
			if (!$result['status']) array_push($log, array('edcenso_professional_education_course' => $result['erro']));

			//campos 40 a 65
			$disciplinesArray = array($column['discipline_chemistry'], $column['discipline_physics'], $column['discipline_mathematics'], $column['discipline_biology'], $column['discipline_science'],
				$column['discipline_language_portuguese_literature'], $column['discipline_foreign_language_english'], $column['discipline_foreign_language_spanish'], $column['discipline_foreign_language_franch'], $column['discipline_foreign_language_other'],
				$column['discipline_arts'], $column['discipline_physical_education'], $column['discipline_history'], $column['discipline_geography'], $column['discipline_philosophy'],
				$column['discipline_social_study'], $column['discipline_sociology'], $column['discipline_informatics'], $column['discipline_professional_disciplines'], $column['discipline_special_education_and_inclusive_practices'],
				$column['discipline_sociocultural_diversity'], $column['discipline_libras'], $column['discipline_pedagogical'], $column['discipline_religious'], $column['discipline_native_language'],
				$column['discipline_others']);
			$result = $crv->isValidDiscipline($disciplinesArray, $column['pedagogical_mediation_type'], $column['assistance_type'], $column['edcenso_stage_vs_modality_fk']);
			if (!$result['status']) array_push($log, array('disciplines' => $result['erro']));
			return $log;
		}
		public function validateInstructor($collumn,$instructor_documents_and_address){
			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}

			$iiv = new InstructorIdentificationValidation();
			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$log = array();

			//campo 1
			$result = $iiv->isRegister("30", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//campo 2
			$result = $iiv->isAllowedInepId($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 3
			if(!empty($collumn['inep_id'])){
				$result = $iiv->isNumericOfSize(12, $collumn['inep_id']);
				if(!$result["status"]) array_push($log, array("inep_id"=>$result["erro"]));
			}

			//campo 4
			$result = $iiv->isNotGreaterThan($collumn['id'], 20);
			if(!$result["status"]) array_push($log, array("id"=>$result["erro"]));

			//campo 5
			$result = $iiv->isNameValid($collumn['name'], 100,
				$instructor_documents_and_address["cpf"]);
			if(!$result["status"]) array_push($log, array("name"=>$result["erro"]));

			//campo 6
			$result = $iiv->isEmailValid($collumn['email'], 100);
			if(!$result["status"]) array_push($log, array("email"=>$result["erro"]));

			//campo 7
			$result = $iiv->isNull($collumn['nis']);
			if(!$result["status"]) array_push($log, array("nis"=>$result["erro"]));

			//campo 8
			$year = Yii::app()->user->year;
			$result = $iiv->validateBirthday($collumn['birthday_date'], "13", "96", $year);
			if(!$result["status"]) array_push($log, array("birthday_date"=>$result["erro"]));

			//campo 9
			$result = $iiv->oneOfTheValues($collumn['sex']);
			if(!$result["status"]) array_push($log, array("sex"=>$result["erro"]));

			//campo 10
			$result = $iiv->isAllowed($collumn['color_race'], array("0", "1", "2", "3", "4", "5"));
			if(!$result["status"]) array_push($log, array("sex"=>$result["erro"]));

			//campo 11, 12, 13
			$result = $iiv->validateFiliation($collumn['filiation'], $collumn['filiation_1'], $collumn['filiation_2'],
				$instructor_documents_and_address["cpf"], 100);
			if(!$result["status"]) array_push($log, array("filiation"=>$result["erro"]));

			//campo 14, 15
			$result = $iiv->checkNation($collumn['nationality'],$collumn['edcenso_nation_fk'], array("1", "2", "3") );
			if(!$result["status"]) array_push($log, array("nationality_nation"=>$result["erro"]));

			//campo 16
			$result = $iiv->ufcity($collumn['nationality'],$collumn['edcenso_nation_fk'],$collumn['edcenso_uf_fk']);
			if(!$result["status"]) array_push($log, array("edcenso_uf_fk"=>$result["erro"]));

			//campo 17 -- @todo melhorar essa checagem
			/*
			$result = $iiv->ufcity($collumn['edcenso_city_fk'], $collumn['nationality']);
			if(!$result["status"]) array_push($log, array("edcenso_uf_fk"=>$result["erro"]));
			*/

			//campo 18
			$result = $iiv->isAllowed($collumn['deficiency'], array("0", "1"));
			if(!$result["status"]) array_push($log, array("deficiency"=>$result["erro"]));

			//campo 19 à 25
			$deficiencies = array($collumn['deficiency_type_blindness'],
				$collumn['deficiency_type_low_vision'],
				$collumn['deficiency_type_deafness'],
				$collumn['deficiency_type_disability_hearing'],
				$collumn['deficiency_type_deafblindness'],
				$collumn['deficiency_type_phisical_disability'],
				$collumn['deficiency_type_intelectual_disability']);

			$excludingdeficiencies = array($collumn['deficiency_type_blindness'] =>
				array($collumn['deficiency_type_low_vision'], $collumn['deficiency_type_deafness'],
					$collumn['deficiency_type_deafblindness']),
				$collumn['deficiency_type_low_vision'] =>
					array($collumn['deficiency_type_deafblindness']),
				$collumn['deficiency_type_deafness'] =>
					array($collumn['deficiency_type_disability_hearing'], $collumn['deficiency_type_disability_hearing']),
				$collumn['deficiency_type_disability_hearing'] =>
					array($collumn['deficiency_type_deafblindness']));

			if(!empty($collumn['deficiency'])){
				$result = $iiv->checkDeficiencies($collumn['deficiency'], $deficiencies, $excludingdeficiencies);
				if(!$result["status"]) array_push($log, array("deficiencies"=>$result["erro"]));
			}

			//campo 26

			$result = $iiv->checkMultiple($collumn['deficiency'], $collumn['deficiency_type_multiple_disabilities'], $deficiencies);
			if(!$result["status"]) array_push($log, array("deficiency_type_multiple_disabilities"=>$result["erro"]));

			return $log;
		}
		public function validateInstructorDocuments($collumn){
			$idav = new InstructorDocumentsAndAddressValidation();
			$log = array();

			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$instructor_inep_id = $collumn["inep_id"];

			//campo 1
			$result = $idav->isRegister("40", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}
			//campo 2
			$result = $idav->isAllowedInepId($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 3
			if(!empty($instructor_inep_id)){
				$sql = "SELECT COUNT(inep_id) AS status FROM instructor_documents_and_address WHERE inep_id =  '$instructor_inep_id'";
				$check = Yii::app()->db->createCommand($sql)->queryAll();
				$result = $idav->isEqual($check[0]['status'],'1', "Não há tal inep_id $instructor_inep_id");
				if(!$result["status"]) array_push($log, array("inep_id"=>$result["erro"]));
			}

			//campo 4
			$result = $idav->isNotGreaterThan($collumn['id'], 20);
			if(!$result["status"]) array_push($log, array("id"=>$result["erro"]));

			$result = $idav->isCPFValid($collumn['cpf']);
			if(!$result["status"]) array_push($log, array("cpf"=>$result["erro"]));

			//campo 5
			if(!empty($collumn['area_of_residence'])){
				//campo 6
				$result = $idav->isAllowed($collumn['area_of_residence'], array("1", "2"));
				if(!$result["status"]) array_push($log, array("area_of_residence"=>$result["erro"]));

				if(!empty($collumn['cep'])){
					//campo 7
					$result = $idav->isCEPValid($collumn['cep']);
					if(!$result["status"]) array_push($log, array("cep"=>$result["erro"]));

					//campo 8
					$result = $idav->isAdressValid($collumn['address'], $collumn['cep'], 100);
					if(!$result["status"]) array_push($log, array("address"=>$result["erro"]));

					//campo 9
					$result = $idav->isAdressValid($collumn['address_number'], $collumn['cep'], 10);
					if(!$result["status"]) array_push($log, array("address_number"=>$result["erro"]));

					//campo 10
					if(!empty($collumn['complement'])){
						$result = $idav->isAdressValid($collumn['complement'], $collumn['cep'], 20);
						if(!$result["status"]) array_push($log, array("complement"=>$result["erro"]));
					}
					//campo 11
					if(!empty($collumn['neighborhood'])) {
						$result = $idav->isAdressValid($collumn['neighborhood'], $collumn['cep'], 50);
						if (!$result["status"]) array_push($log, array("neighborhood" => $result["erro"]));
					}
					//campo 12
					$result = $idav->isAdressValid($collumn['edcenso_uf_fk'], $collumn['cep'], 2);
					if(!$result["status"]) array_push($log, array("edcenso_uf_fk"=>$result["erro"]));

					//campo 13
					$result = $idav->isAdressValid($collumn['edcenso_city_fk'], $collumn['cep'], 7);
					if(!$result["status"]) array_push($log, array("edcenso_city_fk"=>$result["erro"]));
				}

			}






			return $log;
		}
		public function validateInscrutorData($collumn){
			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}

			$itdv = new instructorTeachingDataValidation();
			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$instructor_inep_id = $collumn["instructor_inep_id"];
			$instructor_fk = $collumn['instructor_fk'];
			$classroom_fk = $collumn['classroom_id_fk'];
			$log = array();

			//campo 1
			$result = $itdv->isRegister("51", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//campo 2
			$result = $itdv->isAllowedInepId($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 03
			if(!empty($instructor_inep_id)){
				$sql = "SELECT COUNT(inep_id) AS status FROM instructor_identification WHERE inep_id =  '$instructor_inep_id'";
				$check = Yii::app()->db->createCommand($sql)->queryAll();
				$result = $itdv->isEqual($check[0]['status'],'1', 'Não há tal instructor_inep_id $instructor_inep_id');
				if(!$result["status"]) array_push($log, array("instructor_inep_id"=>$result["erro"]));
			}



			//campo 4
			$sql = "SELECT COUNT(id) AS status FROM instructor_identification WHERE id =  '$instructor_fk'";
			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$result = $itdv->isEqual($check[0]['status'],'1', 'Não há tal instructor_fk $instructor_fk');
			if(!$result["status"]) array_push($log, array("instructor_fk"=>$result["erro"]));

			//campo 5
			$result = $itdv->isNull($collumn['classroom_inep_id']);
			if(!$result["status"]) array_push($log, array("classroom_inep_id"=>$result["erro"]));

			//campo 6
			$sql = "SELECT COUNT(id) AS status FROM classroom WHERE id = '$classroom_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$result = $itdv->isEqual($check[0]['status'],'1', 'Não há tal classroom_id_fk $classroom_fk');
			if(!$result["status"]) array_push($log, array("classroom_id_fk"=>$result["erro"]));

			//campo 7
			$sql = "SELECT assistance_type, pedagogical_mediation_type, edcenso_stage_vs_modality_fk
			FROM classroom
			WHERE id = '$classroom_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();;
			$assistance_type = $check[0]['assistance_type'];
			$pedagogical_mediation_type = $check[0]['pedagogical_mediation_type'];
			$edcenso_svm = $check[0]['edcenso_stage_vs_modality_fk'];

			$sql = "SELECT count(cr.id) AS status_instructor
			FROM 	classroom as cr
						INNER JOIN
					instructor_teaching_data AS itd
						ON itd.classroom_id_fk = cr.id
			WHERE 	cr.id = '$classroom_fk' AND itd.id != '$instructor_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();
			$status_instructor = $check[0]['status_instructor'];


			$sql = "SELECT count(si.id) AS status_student
			FROM 	classroom AS cr
						INNER JOIN
					instructor_teaching_data AS itd
						ON itd.classroom_id_fk = cr.id
						INNER JOIN
					instructor_identification as ii
						ON ii.id = itd.instructor_fk
						INNER JOIN
					student_enrollment AS se
						ON se.classroom_fk =cr.id
						INNER JOIN
					student_identification AS si
					 	on si.id = se.student_fk
			WHERE 	cr.id = '$classroom_fk' AND ii.id = '$instructor_fk'
					AND
					(ii.deficiency_type_deafness = '1' OR ii.deficiency_type_disability_hearing = '1' OR
					ii.deficiency_type_deafblindness = '1' OR si.deficiency_type_deafness = '1' OR
					si.deficiency_type_deafblindness = '1');";
			$check = Yii::app()->db->createCommand($sql)->queryAll();
			$status_student = $check[0]['status_student'];

			$result = $itdv->checkRole($collumn['role'], $pedagogical_mediation_type,
				$assistance_type, $status_instructor, $status_student );
			if(!$result["status"]) array_push($log, array("role"=>$result["erro"]));

			//campo 08
			$sql = "SELECT se.administrative_dependence
			FROM school_identification AS se
			WHERE se.inep_id = '$school_inep_id_fk';";

			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$administrative_dependence = $check[0]['administrative_dependence'];

			$result = $itdv->checkContactType($collumn['contract_type'], $collumn['role'], $administrative_dependence);
			if(!$result["status"]) array_push($log, array("contract_type"=>$result["erro"]));

			//campo 09
			$result = $itdv->disciplineOne($collumn['discipline_1_fk'], $collumn['role'], $assistance_type, $edcenso_svm);
			if(!$result["status"]) array_push($log, array("discipline_1_fk"=>$result["erro"]));

			//campo 09 à 21

			$disciplines_codes = array(	$collumn['discipline_1_fk'],
				$collumn['discipline_2_fk'],
				$collumn['discipline_3_fk'],
				$collumn['discipline_4_fk'],
				$collumn['discipline_5_fk'],
				$collumn['discipline_6_fk'],
				$collumn['discipline_7_fk'],
				$collumn['discipline_8_fk'],
				$collumn['discipline_9_fk'],
				$collumn['discipline_10_fk'],
				$collumn['discipline_11_fk'],
				$collumn['discipline_12_fk'],
				$collumn['discipline_13_fk']);


			$sql = "SELECT discipline_chemistry, discipline_physics, discipline_mathematics, discipline_biology,
						discipline_science, discipline_language_portuguese_literature,
						discipline_foreign_language_english, discipline_foreign_language_spanish,
						discipline_foreign_language_franch, discipline_foreign_language_other,
						discipline_arts, discipline_physical_education, discipline_history, discipline_geography,
						discipline_philosophy, discipline_social_study, discipline_sociology, discipline_informatics,
						discipline_professional_disciplines, discipline_special_education_and_inclusive_practices,
						discipline_sociocultural_diversity, discipline_libras, discipline_pedagogical,
						discipline_religious, discipline_native_language, discipline_others
			FROM 		classroom
			WHERE 	id = '$classroom_fk';";

			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$disciplines = array_values($check[0]);
			$result = $itdv->checkDisciplineCode($disciplines_codes, $collumn['role'], $assistance_type,
				$edcenso_svm, $disciplines);
			if(!$result["status"]) array_push($log, array("disciplines_codes"=>$result["erro"]));

			return $log;
		}
		public function validateStudentIdentification($collumn,$studentdocument){

			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}

			$stiv = new studentIdentificationValidation();
			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$log = array();

			//campo 1
			$result = $stiv->isRegister("60", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//campo 2
			$result = $stiv->isAllowedInepId($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 3
			$result = $stiv->isNumericOfSize(12, $collumn['inep_id']);
			if(!$result["status"]) array_push($log, array("inep_id"=>$result["erro"]));

			//campo 4
			$result = $stiv->isNotGreaterThan($collumn['id'], 20);
			if(!$result["status"]) array_push($log, array("id"=>$result["erro"]));

			//campo 5
			$result = $stiv->isNameValid($collumn['name'], 100,
				$studentdocument["cpf"]);
			if(!$result["status"]) array_push($log, array("name"=>$result["erro"]));

			$year = Yii::app()->user->year;
			//campo 6
			$result = $stiv->validateBirthday($collumn['birthday'], 1910, $year);
			if(!$result["status"]) array_push($log, array("birthday"=>$result["erro"]));

			//campo 7
			$result = $stiv->oneOfTheValues($collumn['sex']);
			if(!$result["status"]) array_push($log, array("sex"=>$result["erro"]));

			//campo 8
			$result = $stiv->isAllowed($collumn['color_race'], array("0", "1", "2", "3", "4", "5"));
			if(!$result["status"]) array_push($log, array("sex"=>$result["erro"]));

			//campo 9, 10, 11
			$result = $stiv->validateFiliation($collumn['filiation'], $collumn['filiation_1'], $collumn['filiation_2'],
				$studentdocument["cpf"], 100);
			if(!$result["status"]) array_push($log, array("filiation"=>$result["erro"]));

			//campo 12, 13
			$result = $stiv->checkNation($collumn['nationality'], $collumn['edcenso_nation_fk'], array("1", "2", "3") );
			if(!$result["status"]) array_push($log, array("nationality_nation"=>$result["erro"]));

			//campo 14
			$result = $stiv->ufcity($collumn['nationality'],$collumn['edcenso_nation_fk'],$collumn['edcenso_uf_fk']);
			if(!$result["status"]) array_push($log, array("edcenso_uf_fk"=>$result["erro"]));

			//campo 15
			$result = $stiv->ufcity($collumn['nationality'],$collumn['edcenso_nation_fk'],$collumn['edcenso_city_fk']);
			if(!$result["status"]) array_push($log, array("edcenso_uf_fk"=>$result["erro"]));

			//campo 16
			$student_id = $collumn['id'];

			$sql = "SELECT 	COUNT(cr.id) AS status
			FROM 	student_identification as si
						INNER JOIN
					student_enrollment AS se
						ON si.id = se.student_fk
          				INNER JOIN
          			classroom AS cr
          				ON se.classroom_fk = cr.id
			WHERE si.id = '$student_id' AND (cr.assistance_type = 5 OR cr.modality = 2)
			GROUP BY si.id;";

			@$hasspecialneeds = Yii::app()->db->createCommand($sql)->queryAll();

			@$result = $stiv->specialNeeds($collumn['deficiency'], array("0", "1"),
				$hasspecialneeds["status"]);
			if(!$result["status"]) array_push($log, array("pedagogical_formation_by_alternance"=>$result["erro"]));

			//campo 17 à 24 e 26 à 29

			$deficiencies_whole = array($collumn['deficiency_type_blindness'],
				$collumn['deficiency_type_low_vision'],
				$collumn['deficiency_type_deafness'],
				$collumn['deficiency_type_disability_hearing'],
				$collumn['deficiency_type_deafblindness'],
				$collumn['deficiency_type_phisical_disability'],
				$collumn['deficiency_type_intelectual_disability'],
				$collumn['deficiency_type_autism'],
				$collumn['deficiency_type_aspenger_syndrome'],
				$collumn['deficiency_type_rett_syndrome'],
				$collumn['deficiency_type_childhood_disintegrative_disorder'],
				$collumn['deficiency_type_gifted']);

			$excludingdeficiencies = array(	$collumn['deficiency_type_blindness'] =>
				array($collumn['deficiency_type_low_vision'], $collumn['deficiency_type_deafness'],
					$collumn['deficiency_type_deafblindness']),
				$collumn['deficiency_type_low_vision'] =>
					array($collumn['deficiency_type_deafblindness']),
				$collumn['deficiency_type_deafness'] =>
					array($collumn['deficiency_type_disability_hearing'], $collumn['deficiency_type_disability_hearing']),
				$collumn['deficiency_type_disability_hearing'] =>
					array($collumn['deficiency_type_deafblindness']),
				$collumn['deficiency_type_autism'] =>
					array($collumn['deficiency_type_aspenger_syndrome'], $collumn['deficiency_type_rett_syndrome'],
						$collumn['deficiency_type_childhood_disintegrative_disorder']),
				$collumn['deficiency_type_aspenger_syndrome'] =>
					array($collumn['deficiency_type_rett_syndrome'], $collumn['deficiency_type_childhood_disintegrative_disorder']),
				$collumn['deficiency_type_rett_syndrome'] =>
					array($collumn['deficiency_type_childhood_disintegrative_disorder']));

			$result = $stiv->checkDeficiencies($collumn['deficiency'], $deficiencies_whole, $excludingdeficiencies);
			if(!$result["status"]) array_push($log, array("deficiencies"=>$result["erro"]));

			//campo 25

			$deficiencies_sample = array($collumn['deficiency_type_blindness'],
				$collumn['deficiency_type_low_vision'],
				$collumn['deficiency_type_deafness'],
				$collumn['deficiency_type_disability_hearing'],
				$collumn['deficiency_type_deafblindness'],
				$collumn['deficiency_type_phisical_disability'],
				$collumn['deficiency_type_intelectual_disability']);

			$result = $stiv->checkMultiple($collumn['deficiency'], $collumn['deficiency_type_multiple_disabilities'], $deficiencies_sample);
			if(!$result["status"]) array_push($log, array("deficiency_type_multiple_disabilities"=>$result["erro"]));

			//campo 30 à 39
			$sql = "SELECT  COUNT(si.id) AS status
			FROM 	student_identification AS si
						INNER JOIN
					student_enrollment AS se
						ON si.id = se.student_fk
			WHERE 	se.edcenso_stage_vs_modality_fk in (16, 7, 18, 11, 41, 27, 28, 32, 33, 37, 38)
					AND si.id = '$student_id';";
			$demandresources = Yii::app()->db->createCommand($sql)->queryAll();

			$resources = array($collumn['resource_aid_lector'],
				$collumn['resource_interpreter_guide'],
				$collumn['resource_interpreter_libras'],
				$collumn['resource_lip_reading'],
				$collumn['resource_zoomed_test_16'],
				$collumn['resource_zoomed_test_20'],
				$collumn['resource_zoomed_test_24'],
				$collumn['resource_braille_test'],
				$collumn['resource_none'],
				$collumn['resource_aid_transcription']);

			array_pop($deficiencies_whole);
			$result = $stiv->inNeedOfResources($deficiencies_whole,
				$demandresources,
				$resources,
				$collumn['deficiency_type_blindness'],
				$collumn['deficiency_type_deafblindness']);
			if(!$result["status"]) array_push($log, array("resources"=>$result["erro"]));
			return $log;
		}
		public function validateStudentDocumentsAddress($collumn,$studentident){
			$student_inep_id = $collumn['student_fk'];
			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}
			$sql = "SELECT inep_id FROM student_identification;";
			$array = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($array as $key => $value) {
				$allowed_students_inep_ids[] = $value['inep_id'];
			}

			$sda = new studentDocumentsAndAddressValidation();
			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$student_inep_id_fk = $collumn["student_fk"];
			$log = array();

			$field6012 = $studentident['nationality'];

			$foreign = $sda->isAllowed($field6012, array("3"));
			$field6006 = $collumn['birthday'];
			$field7005 = $collumn['rg_number'];

			date_default_timezone_set('America/Bahia');
			$date = date('d/m/Y');

			//$sda->isAllowed($collumn['civil_certification'], array("1", "2"));
			$field7009 = $collumn['civil_certification'];


			//campo 1
			$result = $sda->isRegister("70", $collumn['register_type']);
			if (!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//campo 2
			$result = $sda->isAllowedInepId($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 3
			$result = $sda->isAllowedInepId($student_inep_id_fk,
				$allowed_students_inep_ids);
			if(!$result["status"]) array_push($log, array("student_inep_id"=>$result["erro"]));

			//campo 4
			$sql = "SELECT COUNT(inep_id) AS status FROM student_identification WHERE inep_id = '$student_inep_id';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();
			$result = $sda->isEqual($check[0]['status'],'1', "Não há tal student_inep_id $student_inep_id");
			if(!$result["status"]) array_push($log, array("student_fk"=>$result["erro"]));

			//campo 5
			if(!empty($collumn['rg_number'])){
				$result = $sda->isRgNumberValid($collumn['rg_number'], $field6012);
				if(!$result["status"]) array_push($log, array("rg_number"=>$result["erro"]));

				//campo 6
				$result = $sda->isRgEmissorOrganValid($collumn['rg_number_edcenso_organ_id_emitter_fk'], $field6012, $field7005);
				if(!$result["status"]) array_push($log, array("rg_number_edcenso_organ_id_emitter_fk"=>$result["erro"]));

				//campo 7
				$result = $sda->isRgUfValid($collumn['rg_number_edcenso_uf_fk'], $field6012, $field7005);
				if(!$result["status"]) array_push($log, array("rg_number_edcenso_uf_fk"=>$result["erro"]));
			}

			//campo 8
			/*$result = $sda->isDateValid($field6012, $collumn['rg_number_expediction_date'] ,$field6006, $date, 0, 8);
			if(!$result["status"]) array_push($log, array("rg_number_expediction_date"=>$result["erro"]));
			*/

			//campo 9
			$result = $sda->isAllowed($collumn['civil_certification'], array("1", "2"));
			if(!$result["status"]) array_push($log, array("rg_number_expediction_date"=>$result["erro"]));

			//campo 10
			$result = $sda->isCivilCertificationTypeValid($field7009, $field7005, $field6012, $field6006, $date);
			if(!$result["status"]) array_push($log, array("civil_certification_type"=>$result["erro"]));

			//campo 11
			$field7009 = $collumn['civil_certification'];

			if($field7009 == 1){
				$result = $sda->isFieldValid(8, $collumn['civil_certification_term_number'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("civil_certification_term_number"=>$result["erro"]));

				//campo 12
				$result = $sda->isFieldValid(4, $collumn['civil_certification_sheet'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("civil_certification_sheet"=>$result["erro"]));

				//campo 13
				$result = $sda->isFieldValid(8, $collumn['civil_certification_book'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("civil_certification_book"=>$result["erro"]));

				//campo 14
				$result = $sda->isDateValid($field6012, $collumn['civil_certification_date'] ,$field6006, $date, 1, 14);
				if(!$result["status"]) array_push($log, array("civil_certification_date"=>$result["erro"]));

				//campo 15
				$result = $sda->isFieldValid(2, $collumn['notary_office_uf_fk'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("notary_office_uf_fk"=>$result["erro"]));

				//campo 16
				$result = $sda->isFieldValid(7, $collumn['notary_office_city_fk'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("notary_office_city_fk"=>$result["erro"]));

				//campo 17
				$result = $sda->isFieldValid(6, $collumn['edcenso_notary_office_fk'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("edcenso_notary_office_fk"=>$result["erro"]));

			}else{
				//campo 18
				$result = $sda->isCivilRegisterNumberValid($collumn['civil_register_enrollment_number'], $field6012, $field7009);
				if(!$result["status"]) array_push($log, array("civil_register_enrollment_number"=>$result["erro"]));
			}
			//campo 19
			if(!empty($collumn['cpf'])){
				$result = $sda->isCPFValid($collumn['cpf']);
				if(!$result["status"]) array_push($log, array("cpf"=>$result["erro"]));
			}
			//campo 20
			$result = $sda->isPassportValid($collumn['foreign_document_or_passport'], $field6012);
			if(!$result["status"]) array_push($log, array("foreign_document_or_passport"=>$result["erro"]));

			//campo 21
			if(!empty($collumn['nis'])){
				$result = $sda->isNISValid($collumn['nis']);
				if(!$result["status"]) array_push($log, array("nis"=>$result["erro"]));
			}

			//campo 22
			$result = $sda->isAreaOfResidenceValid($collumn['residence_zone']);
			if(!$result["status"]) array_push($log, array("residence_zone"=>$result["erro"]));

			if(!empty($collumn['area_of_residence'])) {
				if(!empty($collumn['cep'])){
					//campo 23
					$result = $sda->isCEPValid($collumn['cep']);
					if (!$result["status"]) array_push($log, array("cep" => $result["erro"]));

					//campo 24
					$result = $sda->isAdressValid($collumn['address'], $collumn['cep'], 100);
					if (!$result["status"]) array_push($log, array("address" => $result["erro"]));

					if(!empty($collumn['number'])){
						//campo 25
						$result = $sda->isAdressValid($collumn['number'], $collumn['cep'], 10);
						if (!$result["status"]) array_push($log, array("number" => $result["erro"]));
					}

					if(!empty($collumn['complement'])){
						//campo 26
						$result = $sda->isAdressValid($collumn['complement'], $collumn['cep'], 20);
						if (!$result["status"]) array_push($log, array("complement" => $result["erro"]));
					}

					if(!empty($collumn['neighborhood'])){
						//campo 27
						$result = $sda->isAdressValid($collumn['neighborhood'], $collumn['cep'], 50);
						if (!$result["status"]) array_push($log, array("neighborhood" => $result["erro"]));
					}

					//campo 28
					$result = $sda->isAdressValid($collumn['edcenso_uf_fk'], $collumn['cep'], 2);
					if (!$result["status"]) array_push($log, array("edcenso_uf_fk" => $result["erro"]));

					//campo 29
					$result = $sda->isAdressValid($collumn['edcenso_city_fk'], $collumn['cep'], 7);
					if (!$result["status"]) array_push($log, array("edcenso_city_fk" => $result["erro"]));
				}

			}
			return $log;
		}
		public function validateEnrollment($collumn){
			$sql = "SELECT inep_id FROM school_identification;";
			$inep_ids = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($inep_ids as $key => $value) {
				$allowed_school_inep_ids[] = $value['inep_id'];
			}
			$sql = "SELECT inep_id FROM student_identification;";
			$array = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($array as $key => $value) {
				$allowed_students_inep_ids[] = $value['inep_id'];
			}

			$sev = new studentEnrollmentValidation();
			$school_inep_id_fk = $collumn["school_inep_id_fk"];
			$student_inep_id_fk = $collumn["student_inep_id"];
			$classroom_fk = $collumn['classroom_fk'];
			$log = array();

			//campo 1
			$result = $sev->isRegister("80", $collumn['register_type']);
			if(!$result["status"]) array_push($log, array("register_type"=>$result["erro"]));

			//campo 2
			$result = $sev->isAllowedInepId($school_inep_id_fk,
				$allowed_school_inep_ids);
			if(!$result["status"]) array_push($log, array("school_inep_id_fk"=>$result["erro"]));

			//campo 3
			$result = $sev->isAllowedInepId($student_inep_id_fk,
				$allowed_students_inep_ids);
			if(!$result["status"]) array_push($log, array("student_inep_id"=>$result["erro"]));

			//campo 4
			$sql = "SELECT COUNT(inep_id) AS status FROM student_identification WHERE inep_id = '$student_inep_id_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();

			if(!empty($student_inep_id)){
				$result = $sev->isEqual($check[0]['status'],'1', "Não há tal student_inep_id $student_inep_id");
				if(!$result["status"]) array_push($log, array("student_fk"=>$result["erro"]));
			}

			//campo 05
			$result = $sev->isNull($collumn['classroom_inep_id']);
			if(!$result["status"]) array_push($log, array("classroom_inep_id"=>$result["erro"]));

			//campo 6

			$sql = "SELECT COUNT(id) AS status FROM classroom WHERE id = '$classroom_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$result = $sev->isEqual($check[0]['status'],'1', 'Não há tal classroom_id $classroom_fk');
			if(!$result["status"]) array_push($log, array("classroom_fk"=>$result["erro"]));

			//campo 07
			$result = $sev->isNull($collumn['enrollment_id']);
			if(!$result["status"]) array_push($log, array("enrollment_id"=>$result["erro"]));

			//campo 8

			$sql = "SELECT COUNT(id) AS status FROM classroom WHERE id = '$classroom_fk' AND edcenso_stage_vs_modality_fk = '3';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$result = $sev->ifDemandsCheckValues($check[0]['status'], $collumn['unified_class'], array('1', '2'));
			if(!$result["status"]) array_push($log, array("unified_class"=>$result["erro"]));

			//campo 9

			$sql = "SELECT edcenso_stage_vs_modality_fk FROM classroom WHERE id = '$classroom_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();

			$edcenso_svm = $check[0]['edcenso_stage_vs_modality_fk'];

			$result = $sev->multiLevel($collumn['edcenso_stage_vs_modality_fk'], $edcenso_svm);
			if(!$result["status"]) array_push($log, array("edcenso_stage_vs_modality_fk"=>$result["erro"]));

			//campo 10
			$sql = "SELECT assistance_type, pedagogical_mediation_type FROM classroom WHERE id = '$classroom_fk';";
			$check = Yii::app()->db->createCommand($sql)->queryAll();
			$assistance_type = $check[0]['assistance_type'];
			$pedagogical_mediation_type = $check[0]['pedagogical_mediation_type'];

			$result = $sev->anotherScholarizationPlace($collumn['another_scholarization_place'], $assistance_type, $pedagogical_mediation_type);
			if(!$result["status"]) array_push($log, array("another_scholarization_place"=>$result["erro"]));

			//campo 11
			//@todo setar nulo na exportação
			if(!empty($collumn['public_transport'])){
				$result = $sev->publicTransportation($collumn['public_transport'], $pedagogical_mediation_type);
				if(!$result["status"]) array_push($log, array("public_transport"=>$result["erro"]));

				$result = $sev->publicTransportation($collumn['public_transport'], $pedagogical_mediation_type);
				if(!$result["status"]) array_push($log, array("public_transport"=>$result["erro"]));

				//campo 12
				$result = $sev->ifDemandsCheckValues($collumn['public_transport'], $collumn['transport_responsable_government'], array('1', '2'));
				if(!$result["status"]) array_push($log, array("transport_responsable_government"=>$result["erro"]));

				//campo 13 à 23

				$vehicules_types = array($collumn['vehicle_type_van'],
					$collumn['vehicle_type_microbus'],
					$collumn['vehicle_type_bus'],
					$collumn['vehicle_type_bike'],
					$collumn['vehicle_type_other_vehicle'],
					$collumn['vehicle_type_waterway_boat_5'],
					$collumn['vehicle_type_waterway_boat_5_15'],
					$collumn['vehicle_type_waterway_boat_15_35'],
					$collumn['vehicle_type_waterway_boat_35'],
					$collumn['vehicle_type_metro_or_train']);

				$result = $sev->vehiculesTypes($collumn['public_transport'], $vehicules_types);
				if(!$result["status"]) array_push($log, array("vehicules_types"=>$result["erro"]));
			}






			//24

			$sql = "SELECT se.administrative_dependence
			FROM school_identification AS se
			WHERE se.inep_id = '$school_inep_id_fk';";

			$check =  Yii::app()->db->createCommand($sql)->queryAll();

			$administrative_dependence = $check[0]['administrative_dependence'];



			$result = $sev->studentEntryForm($collumn['student_entry_form'], $administrative_dependence, $edcenso_svm);
			if(!$result["status"]) array_push($log, array("student_entry_form"=>$result["erro"]));
			return $log;
		}
		public function actionValidate(){
			Yii::import('ext.Validator.*');
			$school = SchoolIdentification::model()->findByPk(Yii::app()->user->school);
			$schoolstructure = SchoolStructure::model()->findByPk(Yii::app()->user->school);
			$schoolcolumn = $school->attributes;
			$schoolstructurecolumn = $schoolstructure->attributes;
			$log['school']['info'] = $school->attributes;
			$log['school']['validate']['identification'] = $this->validateSchool($schoolcolumn);
			$log['school']['validate']['structure'] = $this->validateSchoolStructure($schoolstructurecolumn,$schoolcolumn);
			$classrooms = Classroom::model()->findAllByAttributes(["school_inep_fk" => yii::app()->user->school, "school_year" => Yii::app()->user->year]);
			foreach ($classrooms as $iclass => $classroom) {
				$log['classroom'][$iclass]['info'] = $classroom->attributes;
				$log['classroom'][$iclass]['validate']['identification'] = $this->validateClassroom($classroom,$schoolcolumn,$schoolstructure);
				foreach ($classroom->instructorTeachingDatas as $iteaching => $teachingData) {
					if(!isset($log['instructor'][$teachingData->instructor_fk])){
						$log['instructor'][$teachingData->instructor_fk]['info'] = $teachingData->instructorFk->attributes;
						$log['instructor'][$teachingData->instructor_fk]['validate']['identification'] = $this->validateInstructor($teachingData->instructorFk->attributes,$teachingData->instructorFk->documents->attributes);
						$log['instructor'][$teachingData->instructor_fk]['validate']['documents'] = $this->validateInstructorDocuments($teachingData->instructorFk->documents->attributes);
					}
					$log['instructor'][$teachingData->instructor_fk]['validate']['variabledata'][$iteaching]['id'] = $teachingData->classroomIdFk->id;
					$log['instructor'][$teachingData->instructor_fk]['validate']['variabledata'][$iteaching]['turma'] = $teachingData->classroomIdFk->name;
					$log['instructor'][$teachingData->instructor_fk]['validate']['variabledata'][$iteaching]['errors'] = $this->validateInscrutorData($teachingData->attributes);
				}
				foreach ($classroom->studentEnrollments as $ienrollment => $enrollment) {
					if(!isset($log['student'][$enrollment->student_fk]['info'])){
						$log['student'][$enrollment->student_fk]['info'] = $enrollment->studentFk->attributes;
						$log['student'][$enrollment->student_fk]['validate']['identification'] = $this->validateStudentIdentification($enrollment->studentFk->attributes,$enrollment->studentFk->documentsFk->attributes);
						$log['student'][$enrollment->student_fk]['validate']['documents'] = $this->validateStudentDocumentsAddress($enrollment->studentFk->documentsFk->attributes,$enrollment->studentFk->attributes);
					}
					$log['student'][$enrollment->student_fk]['validate']['enrollment'][$ienrollment]['id'] = $enrollment->id;
					$log['student'][$enrollment->student_fk]['validate']['enrollment'][$ienrollment]['turma'] = $enrollment->classroomFk->name;
					$log['student'][$enrollment->student_fk]['validate']['enrollment'][$ienrollment]['errors'] = $this->validateEnrollment($enrollment->attributes);
				}
			}
			$this->render('validate', ['log' => $log]);
		}


	}

?>
