import React, { Component } from 'react';
import { Button } from 'reactstrap';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { previewReport, deleteReport, getReport } from 'Actions';

class Card extends Component{

    constructor(props){
        super(props);
        this.state={
            showActions: false
        }
    }

    handleClick = () => {
        this.setState({ showActions: !this.state.showActions });
    };

    handleClose = () => {
        this.setState({ showActions: false });
    };

    deleteReport = async (id) => {
        await this.props.deleteReport(id, () =>{
            this.props.getReport();
        });
    }


    render(){
        const {id, createdAt} = this.props;
        return(
            <div className="col-sm-12 col-md-4 cursor-pointer" >
                <div className="rct-block">
                    <div className="rct-block-content">
                    {
                        this.state.showActions ?(
                            <div className="row mx-0 w-100" onClick = {this.handleClose}>
                                <div className="col-12 d-flex justify-content-center aling-items-center">
                                    <Button className="mr-2" color="primary" onClick={ () => this.props.previewReport(this.props.id)} >Visualizar</Button>
                                    <Button color="danger" onClick={ () => this.deleteReport(this.props.id)} >Excluir</Button>
                                </div>
                            </div>
                        ): (

                            <div className="row mx-0" onClick = {this.handleClick}>
                                <div className="col-7 d-flex aling-items-center">
                                    <div className="mr-2">
                                        <img width="26px" src={require('../../assets/img/icons/student.png')} />
                                    </div>
                                    <div className="w-100">
                                        <h4 className="report-card-title text-ellipsis">{id}</h4>
                                        <span className="report-card-subtitle">Identificador</span>
                                    </div>
                                </div>
                                <div className="col-4 d-flex aling-items-center">
                                    <div className="mr-2">
                                        <img width="26px" src={require('../../assets/img/icons/calendar.png')} />
                                    </div>
                                    <div>
                                        <h4 className="report-card-title">{createdAt.split(' ')[0]}</h4>
                                        <span className="report-card-subtitle">Criado em</span>
                                    </div>
                                </div>
                            </div>
                            )
                        }
                    </div>
                </div>
            </div>
        )
    }
}

Card.propTypes = {
    id: PropTypes.string.isRequired,
    createdAt: PropTypes.string.isRequired,
};

const mapStateToProps = ({ report }) => {
    return report;
 };

export default connect(mapStateToProps, {
    previewReport: previewReport,
    deleteReport: deleteReport,
    getReport: getReport,
 })(Card);