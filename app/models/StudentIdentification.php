<?php

/**
 * This is the model class for table "student_identification".
 *
 * The followings are the available columns in table 'student_identification':
 * @property string $register_type
 * @property string $school_inep_id_fk
 * @property string $inep_id
 * @property integer $id
 * @property string $name
 * @property string $birthday
 * @property integer $sex
 * @property integer $color_race
 * @property integer $filiation
 * @property integer $id_email
 * @property integer $scholarity
 * @property string $filiation_1
 * @property string $filiation_2
 * @property integer $nationality
 * @property integer $edcenso_nation_fk
 * @property integer $edcenso_uf_fk
 * @property integer $edcenso_city_fk
 * @property integer $college_post_graduation_specialization
 * @property integer $college_post_graduation_master
 * @property integer $college_post_graduation_doctorate
 * @property integer $college_post_graduation_education_manager
 * @property integer $college_other_courses_pre_school
 * @property integer $college_other_courses_basic_education_initial_years
 * @property integer $college_other_courses_basic_education_final_years
 * @property integer $college_other_courses_high_school
 * @property integer $college_other_courses_education_of_youth_and_adults
 * @property integer $college_other_courses_special_education
 * @property integer $college_other_courses_native_education
 * @property integer $college_other_courses_field_education
 * @property integer $college_other_courses_environment_education
 * @property integer $college_other_courses_human_rights_education
 * @property integer $college_other_courses_sexual_education
 * @property integer $college_other_courses_child_and_teenage_rights
 * @property integer $college_other_courses_ethnic_education
 * @property integer $college_other_courses_other
 * @property integer $deficiency
 * @property integer $deficiency_type_blindness
 * @property integer $deficiency_type_low_vision
 * @property integer $deficiency_type_deafness
 * @property integer $deficiency_type_disability_hearing
 * @property integer $deficiency_type_deafblindness
 * @property integer $deficiency_type_phisical_disability
 * @property integer $deficiency_type_intelectual_disability
 * @property integer $deficiency_type_multiple_disabilities
 * @property integer $deficiency_type_autism
 * @property integer $deficiency_type_aspenger_syndrome
 * @property integer $deficiency_type_rett_syndrome
 * @property integer $deficiency_type_childhood_disintegrative_disorder
 * @property integer $deficiency_type_gifted
 * @property integer $resource_aid_lector
 * @property integer $resource_aid_transcription
 * @property integer $resource_interpreter_guide
 * @property integer $resource_interpreter_libras
 * @property integer $resource_lip_reading
 * @property integer $resource_zoomed_test_16
 * @property integer $resource_zoomed_test_20
 * @property integer $resource_zoomed_test_24
 * @property integer $resource_zoomed_test_18
 * @property integer $resource_braille_test
 * @property integer $resource_proof_language
 * @property integer $resource_cd_audio
 * @property integer $resource_video_libras
 * @property integer $resource_none
 * @property integer $send_year
 * @property strign $last_change
 * @property integer $responsable
 * @property string $responsable_name
 * @property string $responsable_rg
 * @property string $responsable_cpf
 * @property integer $responsable_scholarity
 * @property string $responsable_job
 * @property integer $bf_participator
 * @property string $food_restrictions
 * @property string $responsable_telephone
 * @property string $tag_id
 * @property integer $no_documents_desc
 * @property string $fkid
 *
 * The followings are the available model relations:
 * @property StudentEnrollment[] $studentEnrollments
 * @property EdcensoNation $edcensoNationFk
 * @property EdcensoUf $edcensoUfFk
 * @property EdcensoCity $edcensoCityFk
 * @property SchoolIdentification $schoolInepIdFk
 * @property StudentDocumentsAndAddress $documentsFk
 */
class StudentIdentification extends AltActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return StudentIdentification the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'student_identification';
    }

    public function behaviors() {
        if(isset(Yii::app()->user->school)){
            return [
                'afterSave'=>[
                    'class'=>'application.behaviors.CAfterSaveBehavior',
                    'schoolInepId' => Yii::app()->user->school,
                ],
            ];
        }else{
            return [];
        }
    }
        
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('school_inep_id_fk, name, birthday, sex, color_race, filiation, nationality, edcenso_nation_fk, deficiency, send_year', 'required'),
            array('sex, color_race, filiation, scholarity, nationality, edcenso_nation_fk, edcenso_uf_fk, edcenso_city_fk, deficiency, college_post_graduation_specialization, college_post_graduation_master, college_post_graduation_doctorate, college_post_graduation_education_manager, college_other_courses_pre_school, college_other_courses_basic_education_initial_years, college_other_courses_basic_education_final_years, college_other_courses_high_school, college_other_courses_education_of_youth_and_adults, college_other_courses_native_education, college_other_courses_field_education, college_other_courses_environment_education, college_other_courses_human_rights_education, college_other_courses_sexual_education, college_other_courses_child_and_teenage_rights, college_other_courses_ethnic_education, college_other_courses_other, college_other_courses_special_education, deficiency_type_blindness, deficiency_type_low_vision, deficiency_type_deafness, deficiency_type_disability_hearing, deficiency_type_deafblindness, deficiency_type_phisical_disability, deficiency_type_intelectual_disability, deficiency_type_multiple_disabilities, deficiency_type_autism, deficiency_type_aspenger_syndrome, deficiency_type_rett_syndrome, deficiency_type_childhood_disintegrative_disorder, deficiency_type_gifted, resource_aid_lector, resource_aid_transcription, resource_interpreter_guide, resource_interpreter_libras, resource_lip_reading, resource_zoomed_test_16, resource_zoomed_test_20, resource_zoomed_test_24, resource_zoomed_test_18, resource_braille_test, resource_proof_language, resource_cd_audio, resource_video_libras, resource_none, send_year, responsable, responsable_scholarity, filiation_1_scholarity, filiation_2_scholarity, bf_participator, no_documents_desc', 'numerical', 'integerOnly'=>true),
            array('register_type', 'length', 'max'=>2),
            array('school_inep_id_fk', 'length', 'max'=>8),
            array('inep_id', 'length', 'max'=>12),
            array('name, filiation_1, filiation_2', 'length', 'max'=>100),
            array('id_email', 'length', 'max'=>255),
            array('id_email', 'email'),
            array('birthday', 'length', 'max'=>10),
            array('responsable_name', 'length', 'max'=>90),
            array('responsable_rg, responsable_job, filiation_1_rg, filiation_2_rg, filiation_1_job, filiation_2_job', 'length', 'max'=>45),
            array('responsable_cpf, responsable_telephone, filiation_1_cpf, filiation_2_cpf', 'length', 'max'=>11),
            array('hash', 'length', 'max'=>40),
            array('last_change, food_restrictions', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('register_type, school_inep_id_fk, inep_id, id, name, birthday, sex, color_race, filiation, id_email, scholarity, filiation_1, filiation_2, nationality, edcenso_nation_fk, edcenso_uf_fk, edcenso_city_fk, deficiency, college_post_graduation_specialization, college_post_graduation_master, college_post_graduation_doctorate, college_post_graduation_education_manager, college_other_courses_pre_school, college_other_courses_basic_education_initial_years, college_other_courses_basic_education_final_years, college_other_courses_high_school, college_other_courses_education_of_youth_and_adults, college_other_courses_native_education, college_other_courses_field_education, college_other_courses_environment_education, college_other_courses_human_rights_education, college_other_courses_sexual_education, college_other_courses_child_and_teenage_rights, college_other_courses_ethnic_education, college_other_courses_other, college_other_courses_special_education, deficiency_type_blindness, deficiency_type_low_vision, deficiency_type_deafness, deficiency_type_disability_hearing, deficiency_type_deafblindness, deficiency_type_phisical_disability, deficiency_type_intelectual_disability, deficiency_type_multiple_disabilities, deficiency_type_autism, deficiency_type_aspenger_syndrome, deficiency_type_rett_syndrome, deficiency_type_childhood_disintegrative_disorder, deficiency_type_gifted, resource_aid_lector, resource_aid_transcription, resource_interpreter_guide, resource_interpreter_libras, resource_lip_reading, resource_zoomed_test_16, resource_zoomed_test_20, resource_zoomed_test_24, resource_zoomed_test_18, resource_braille_test, resource_proof_language, resource_cd_audio, resource_video_libras, resource_none, send_year, last_change, responsable, responsable_name, responsable_rg, responsable_cpf, responsable_scholarity, responsable_job, bf_participator, food_restrictions, responsable_telephone, fkid, no_documents_desc', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'edcensoNationFk' => array(self::BELONGS_TO, 'EdcensoNation', 'edcenso_nation_fk'),
            'edcensoUfFk' => array(self::BELONGS_TO, 'EdcensoUf', 'edcenso_uf_fk'),
            'edcensoCityFk' => array(self::BELONGS_TO, 'EdcensoCity', 'edcenso_city_fk'),
            'schoolInepIdFk' => array(self::BELONGS_TO, 'SchoolIdentification', 'school_inep_id_fk'),
            'documentsFk' => array(self::BELONGS_TO, 'StudentDocumentsAndAddress', 'id'),
            'studentEnrollments' => array(self::HAS_MANY, 'StudentEnrollment', 'student_fk','order'=>'id DESC')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'register_type' => Yii::t('default', 'Register Type'),
            'school_inep_id_fk' => Yii::t('default', 'School Inep Id Fk'),
            'inep_id' => Yii::t('default', 'ID INEP'),
            'id' => Yii::t('default', 'ID'),
            'name' => Yii::t('default', 'Name'),
            'birthday' => Yii::t('default', 'Birthday'),
            'sex' => Yii::t('default', 'Sex'),
            'color_race' => Yii::t('default', 'Color Race'),
            'filiation' => Yii::t('default', 'Filiation'),
            'id_email' => Yii::t('default', 'Id Email'),
            'scholarity' => Yii::t('default', 'Scholarity'),
            'filiation_1' => Yii::t('default', 'Filiation 1'),
            'filiation_2' => Yii::t('default', 'Filiation 2'),
            'nationality' => Yii::t('default', 'Nationality'),
            'edcenso_nation_fk' => Yii::t('default', 'Edcenso Nation Fk'),
            'edcenso_uf_fk' => Yii::t('default', 'Edcenso Uf Fk'),
            'edcenso_city_fk' => Yii::t('default', 'Edcenso City Fk'),
            'deficiency' => Yii::t('default', 'Deficiency'),
            'college_post_graduation_specialization' => Yii::t('default', 'College Post Graduation Specialization'),
            'college_post_graduation_master' => Yii::t('default', 'College Post Graduation Master'),
            'college_post_graduation_doctorate' => Yii::t('default', 'College Post Graduation Doctorate'),
            'college_post_graduation_education_manager' => Yii::t('default', 'College Post Graduation Education Manager'),
            'college_other_courses_pre_school' => Yii::t('default', 'College Other Courses Pre School'),
            'college_other_courses_basic_education_initial_years' => Yii::t('default', 'College Other Courses Basic Education Initial Years'),
            'college_other_courses_basic_education_final_years' => Yii::t('default', 'College Other Courses Basic Education Final Years'),
            'college_other_courses_high_school' => Yii::t('default', 'College Other Courses High School'),
            'college_other_courses_education_of_youth_and_adults' => Yii::t('default', 'College Other Courses Education Of Youth And Adults'),
            'college_other_courses_special_education' => Yii::t('default', 'College Other Courses Special Education'),
            'college_other_courses_native_education' => Yii::t('default', 'College Other Courses Native Education'),
            'college_other_courses_field_education' => Yii::t('default', 'College Other Courses Field Education'),
            'college_other_courses_environment_education' => Yii::t('default', 'College Other Courses Environment Education'),
            'college_other_courses_human_rights_education' => Yii::t('default', 'College Other Courses Human Rights Education'),
            'college_other_courses_sexual_education' => Yii::t('default', 'College Other Courses Sexual Education'),
            'college_other_courses_child_and_teenage_rights' => Yii::t('default', 'College Other Courses Child And Teenage Rights'),
            'college_other_courses_ethnic_education' => Yii::t('default', 'College Other Courses Ethnic Education'),
            'college_other_courses_other' => Yii::t('default', 'College Other Courses Other'),
            'deficiency_type_blindness' => Yii::t('default', 'Deficiency Type Blindness'),
            'deficiency_type_low_vision' => Yii::t('default', 'Deficiency Type Low Vision'),
            'deficiency_type_deafness' => Yii::t('default', 'Deficiency Type Deafness'),
            'deficiency_type_disability_hearing' => Yii::t('default', 'Deficiency Type Disability Hearing'),
            'deficiency_type_deafblindness' => Yii::t('default', 'Deficiency Type Deafblindness'),
            'deficiency_type_phisical_disability' => Yii::t('default', 'Deficiency Type Phisical Disability'),
            'deficiency_type_intelectual_disability' => Yii::t('default', 'Deficiency Type Intelectual Disability'),
            'deficiency_type_multiple_disabilities' => Yii::t('default', 'Deficiency Type Multiple Disabilities'),
            'deficiency_type_autism' => Yii::t('default', 'Deficiency Type Autism'),
            'deficiency_type_aspenger_syndrome' => Yii::t('default', 'Deficiency Type Aspenger Syndrome'),
            'deficiency_type_rett_syndrome' => Yii::t('default', 'Deficiency Type Rett Syndrome'),
            'deficiency_type_childhood_disintegrative_disorder' => Yii::t('default', 'Deficiency Type Childhood Disintegrative Disorder'),
            'deficiency_type_gifted' => Yii::t('default', 'Deficiency Type Gifted'),
            'resource_aid_lector' => Yii::t('default', 'Resource Aid Lector'),
            'resource_aid_transcription' => Yii::t('default', 'Resource Aid Transcription'),
            'resource_interpreter_guide' => Yii::t('default', 'Resource Interpreter Guide'),
            'resource_interpreter_libras' => Yii::t('default', 'Resource Interpreter Libras'),
            'resource_lip_reading' => Yii::t('default', 'Resource Lip Reading'),
            'resource_zoomed_test_16' => Yii::t('default', 'Resource Zoomed Test 16'),
            'resource_zoomed_test_20' => Yii::t('default', 'Resource Zoomed Test 20'),
            'resource_zoomed_test_24' => Yii::t('default', 'Resource Zoomed Test 24'),
            'resource_zoomed_test_18' => Yii::t('default', 'Resource Zoomed Test 18'),
            'resource_braille_test' => Yii::t('default', 'Resource Braille Test'),
            'resource_proof_language' => Yii::t('default', 'Resource Proof Language'),
            'resource_cd_audio' => Yii::t('default', 'Resource Cd Audio'),
            'resource_video_libras' => Yii::t('default', 'Resource Video Libras'),
            'resource_none' => Yii::t('default', 'Resource None'),
            'send_year' => Yii::t('default', 'Pós Censo'),
            'last_change' => Yii::t('default', 'Last Change'),
            'responsable' => Yii::t('default', 'Responsable'),
            'responsable_telephone' => Yii::t('default', "Responsible's Telephone"),
            'responsable_name' => Yii::t('default', 'Responsable`s Name'),
            'responsable_rg' => Yii::t('default', 'Responsable`s RG'),
            'responsable_cpf' => Yii::t('default', 'Responsable`s CPF'),
            'responsable_scholarity' => Yii::t('default', 'Responsable`s Scholarity'),
            'responsable_job' => Yii::t('default', 'Responsable`s Job'),
            'bf_participator' => Yii::t('default', 'BF Participator'),
            'food_restrictions' => Yii::t('default', 'Food Restrictions'),
            'filiation_1_rg' => Yii::t('default', 'Mother RG'),
            'filiation_1_cpf' => Yii::t('default', 'Mother CPF'),
            'filiation_1_scholarity' => Yii::t('default', 'Mother Scholarity'),
            'filiation_1_job' => Yii::t('default', 'Mother Job'),
            'filiation_2_rg' => Yii::t('default', 'Father RG'),
            'filiation_2_cpf' => Yii::t('default', 'Father CPF'),
            'filiation_2_scholarity' => Yii::t('default', 'Father Scholarity'),
            'filiation_2_job' => Yii::t('default', 'Father Job'),
            'no_documents_desc' => Yii::t('default', 'No Documents Desc')
            
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('register_type', $this->register_type, true);
//        $school = Yii::app()->user->school;
//        $criteria->compare('school_inep_id_fk', $school);
        $criteria->compare('inep_id', $this->inep_id, true);
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
//        $criteria->compare('nis', $this->nis, true);
//        $criteria->compare('birthday', $this->birthday, true);
//        $criteria->compare('sex', $this->sex);
//        $criteria->compare('color_race', $this->color_race);
//        $criteria->compare('filiation', $this->filiation);
//        $criteria->compare('id_email', $this->id_email);
//        $criteria->compare('scholarity', $this->scholarity);
        $criteria->compare('filiation_1', $this->filiation_1, true);
//        $criteria->compare('father_name', $this->father_name, true);
//        $criteria->compare('nationality', $this->nationality);
//        $criteria->compare('edcenso_nation_fk', $this->edcenso_nation_fk);
//        $criteria->compare('edcenso_uf_fk', $this->edcenso_uf_fk);
//        $criteria->compare('edcenso_city_fk', $this->edcenso_city_fk);
//        $criteria->compare('deficiency', $this->deficiency);
//        $criteria->compare('college_post_graduation_specialization', $this->college_post_graduation_specialization);
//        $criteria->compare('college_post_graduation_master', $this->college_post_graduation_master);
//        $criteria->compare('college_post_graduation_doctorate', $this->college_post_graduation_doctorate);
//        $criteria->compare('college_post_graduation_education_manager', $this->college_post_graduation_education_manager);
//        $criteria->compare('college_other_courses_pre_school', $this->college_other_courses_pre_school);
//        $criteria->compare('college_other_courses_basic_education_initial_years', $this->college_other_courses_basic_education_initial_years);
//        $criteria->compare('college_other_courses_basic_education_final_years', $this->college_other_courses_basic_education_final_years);
//        $criteria->compare('college_other_courses_high_school', $this->college_other_courses_high_school);
//        $criteria->compare('college_other_courses_education_of_youth_and_adults', $this->college_other_courses_education_of_youth_and_adults);
//        $criteria->compare('college_other_courses_special_education', $this->college_other_courses_special_education);
//        $criteria->compare('college_other_courses_native_education', $this->college_other_courses_native_education);
//        $criteria->compare('college_other_courses_field_education', $this->college_other_courses_field_education);
//        $criteria->compare('college_other_courses_environment_education', $this->college_other_courses_environment_education);
//        $criteria->compare('college_other_courses_human_rights_education', $this->college_other_courses_human_rights_education);
//        $criteria->compare('college_other_courses_sexual_education', $this->college_other_courses_sexual_education);
//        $criteria->compare('college_other_courses_child_and_teenage_rights', $this->college_other_courses_child_and_teenage_rights);
//        $criteria->compare('college_other_courses_ethnic_education', $this->college_other_courses_ethnic_education);
//        $criteria->compare('college_other_courses_other', $this->college_other_courses_other);
//        $criteria->compare('deficiency_type_blindness', $this->deficiency_type_blindness);
//        $criteria->compare('deficiency_type_low_vision', $this->deficiency_type_low_vision);
//        $criteria->compare('deficiency_type_deafness', $this->deficiency_type_deafness);
//        $criteria->compare('deficiency_type_disability_hearing', $this->deficiency_type_disability_hearing);
//        $criteria->compare('deficiency_type_deafblindness', $this->deficiency_type_deafblindness);
//        $criteria->compare('deficiency_type_phisical_disability', $this->deficiency_type_phisical_disability);
//        $criteria->compare('deficiency_type_intelectual_disability', $this->deficiency_type_intelectual_disability);
//        $criteria->compare('deficiency_type_multiple_disabilities', $this->deficiency_type_multiple_disabilities);
//        $criteria->compare('deficiency_type_autism', $this->deficiency_type_autism);
//        $criteria->compare('deficiency_type_aspenger_syndrome', $this->deficiency_type_aspenger_syndrome);
//        $criteria->compare('deficiency_type_rett_syndrome', $this->deficiency_type_rett_syndrome);
//        $criteria->compare('deficiency_type_childhood_disintegrative_disorder', $this->deficiency_type_childhood_disintegrative_disorder);
//        $criteria->compare('deficiency_type_gifted', $this->deficiency_type_gifted);
//        $criteria->compare('resource_aid_lector', $this->resource_aid_lector);
//        $criteria->compare('resource_aid_transcription', $this->resource_aid_transcription);
//        $criteria->compare('resource_interpreter_guide', $this->resource_interpreter_guide);
//        $criteria->compare('resource_interpreter_libras', $this->resource_interpreter_libras);
//        $criteria->compare('resource_lip_reading', $this->resource_lip_reading);
//        $criteria->compare('resource_zoomed_test_16', $this->resource_zoomed_test_16);
//        $criteria->compare('resource_zoomed_test_20', $this->resource_zoomed_test_20);
//        $criteria->compare('resource_zoomed_test_24', $this->resource_zoomed_test_24);
//        $criteria->compare('resource_zoomed_test_18', $this->resource_zoomed_test_18);
//        $criteria->compare('resource_braille_test', $this->resource_braille_test);
//        $criteria->compare('resource_proof_language', $this->resource_proof_language);
//        $criteria->compare('resource_cd_audio', $this->resource_cd_audio);
//        $criteria->compare('resource_video_libras', $this->resource_video_libras);
//        $criteria->compare('resource_none', $this->resource_none);
//        $criteria->compare('send_year', $this->send_year);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => array(
                    'name' => CSort::SORT_ASC
                ),
            ),
            'pagination' => array(
                'pageSize' => 15,
            ),
        ));
    }

    public function getConcatened() {
        return $this->name . ' (' . $this->filiation_1.')['.$this->birthday.']';
    }

    /**
     * This method is invoked before saving a record (after validation, if any).
     * @return boolean whether the saving should be executed. Defaults to true.
 */
    protected function beforeSave() {

        return parent::beforeSave();
    }


    public function getCurrentStageVsModality(){
        $sid = isset($this->id) ? $this->id : 0;
        $sql = "select student_fk student, se.id enrollment, se.edcenso_stage_vs_modality_fk enrollment_svm, c.edcenso_stage_vs_modality_fk classroom_svm from student_enrollment se
                  join classroom c on c.id = se.classroom_fk
                where se.student_fk = $sid
                order by school_year desc;";
        $result = Yii::app()->db->createCommand($sql)->queryRow();

        $stage = null;
        if(isset($result)){
            $stage = isset($result['enrollment_svm']) ? $result['enrollment_svm'] : $result['classroom_svm'];
        }
        return $stage;

    }
}
