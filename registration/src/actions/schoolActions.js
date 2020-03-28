import * as actions from "./schoolTypes";

export const getSchools = response => ({
  type: actions.GET_ALL,
  payload: response
});

export const getSchool = response => ({
  type: actions.GET_SCHOOL,
  payload: response
});

export const getError = error => ({
  type: actions.GET_ERROR_SCHOOL,
  payload: "Erro: " + error + ". Por favor, tente novamente."
});

export const closeAlert = () => ({
  type: actions.CLOSE_ALERT_SCHOOL
});