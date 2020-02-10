import React, { useState, useEffect } from "react";
import { Schedule } from "../../screens/Schedule";
import { connect } from "react-redux";

const Home = props => {
  const [loadData, setLoadData] = useState(true);
  const [loadDataPaginate, setLoadDataPaginate] = useState(false);
  const [page, setPage] = useState(0);

  const handlePage = (e, pageInfo) => {
    setLoadDataPaginate(true);
    setPage(pageInfo.activePage);
  };

  useEffect(() => {
    if (loadData) {
      props.dispatch({ type: "FETCH_SCHEDULES" });
      setLoadData(false);
    }

    if (loadDataPaginate) {
      props.dispatch({ type: "FETCH_SCHEDULES_PAGE", page });
      setLoadDataPaginate(false);
    }
  }, [loadData, page, loadDataPaginate, props]);

  return (
    <Schedule
      pagination={props.schedule.pagination}
      data={props.schedule.schedules}
      handlePage={handlePage}
    />
  );
};

const mapStateToProps = state => {
  return {
    schedule: state.schedule.schedules
  };
};
export default connect(mapStateToProps)(Home);
