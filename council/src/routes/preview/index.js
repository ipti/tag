import React from 'react';
import { Route, Switch } from 'react-router-dom';

import {
    AsyncPreviewNotificationContainer,
    AsyncPreviewFoodContainer,
    AsyncPreviewWarningContainer,
    AsyncPreviewServiceContainer,
    AsyncPreviewReportContainer,
    AsyncPreviewHousingContainer,
    AsyncPreviewFactContainer,
} from 'Components/AsyncComponent/AsyncComponent';

import 'Assets/css/preview/style.css';

const Preview = ({match}) => (
    <div className="page">
        <Switch>
            <Route exact={true} path={`${match.url}/notification/:id`} component={AsyncPreviewNotificationContainer} />
            <Route exact={true} path={`${match.url}/food/:id`} component={AsyncPreviewFoodContainer} />
            <Route exact={true} path={`${match.url}/warning/:id`} component={AsyncPreviewWarningContainer} />
            <Route exact={true} path={`${match.url}/service/:id`} component={AsyncPreviewServiceContainer} />
            <Route exact={true} path={`${match.url}/report/:id`} component={AsyncPreviewReportContainer} />
            <Route exact={true} path={`${match.url}/housing/:id`} component={AsyncPreviewHousingContainer} />
            <Route exact={true} path={`${match.url}/fact/:id`} component={AsyncPreviewFactContainer} />
        </Switch>
    </div>
);

export default Preview;