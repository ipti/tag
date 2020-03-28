/**
 * Root Sagas
 */
import { all } from 'redux-saga/effects';

// sagas
import authSagas from './Auth';
import emailSagas from './Email';
import todoSagas from './Todo';
import feedbacksSagas from './Feedbacks';
import PeopleSagas from './People';
import NotificationSagas from './Notification';
import ServiceSagas from './Service';
import FoodSagas from './Food';
import WarningSagas from './Warning';
import InstitutionSagas from './Institution';
import HousingSagas from './Housing';
import FactSagas from './Fact';
import ReportSagas from './Report';
import AttendanceSagas from './Attendance';

export default function* rootSaga(getState) {
    yield all([
        authSagas(),
        emailSagas(),
        todoSagas(),
        feedbacksSagas(),
        PeopleSagas(),
        NotificationSagas(),
        FoodSagas(),
        WarningSagas(),
        ServiceSagas(),
        InstitutionSagas(),
        HousingSagas(),
        FactSagas(),
        ReportSagas(),
        AttendanceSagas(),
    ]);
}