import React, { useState, useEffect } from "react";
import * as Yup from "yup";
import { ClassroomForm } from "../../screens/Classroom";
import { connect } from "react-redux";
import Alert from "../../components/Alert/CustomizedSnackbars";

const Form = props => {
  const [loadData, setLoadData] = useState(true);
  const [open, setOpen] = useState(false);
  const [isEdit, setIsEdit] = useState(false);

  useEffect(() => {
    if (props.match.params.id && loadData) {
      props.dispatch({
        type: "FETCH_CLASSROOM",
        data: { id: props.match.params.id }
      });
      setIsEdit(true);
      setLoadData(false);
    }

    if (props?.error) {
      setOpen(true);
    }
  }, [isEdit, loadData, props]);

  const handleClose = () => {
    setOpen(false);
  };

  const alert = () => {
    let status = null;
    let message = null;

    if (props?.error) {
      status = 0;
      message = props.error;
    } else {
      if (props.fetchClassroom) {
        status = props.fetchClassroom.status;
        message = props.fetchClassroom.message;
      }
    }

    return (
      <Alert
        open={open}
        handleClose={handleClose}
        status={status}
        message={message}
      />
    );
  };

  const handleSubmit = values => {
    props.dispatch({
      type: "FETCH_UPDATE_CLASSROOM",
      data: values,
      id: props.match.params.id
    });
    setOpen(true);
    setLoadData(true);
  };

  const validationSchema = Yup.object().shape({
    vacancies: Yup.number()
      .nullable()
      .required("Campo obrigatório!")
  });

  const initialValues = {
    vacancies: props.classroom?.classroom?.data
      ? props.classroom?.classroom?.data.vacancies
      : ""
  };

  return (
    <>
      <ClassroomForm
        initialValues={initialValues}
        validationSchema={validationSchema}
        handleSubmit={handleSubmit}
        baseLink={`/turmas/${props.match.params.id}/matricula`}
        isEdit={isEdit}
        data={
          Object.keys(props.classroom.classroom).length > 0
            ? props.classroom.classroom.data
            : null
        }
      />
      {alert()}
    </>
  );
};

const mapStateToProps = state => {
  return {
    classroom: state.classroom,
    fetchClassroom: state.classroom.fetchClassroom,
    error: state.classroom.msgError
  };
};

export default connect(mapStateToProps)(Form);
