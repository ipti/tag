import React, { Component } from 'react';
import RctCollapsibleCard from 'Components/RctCollapsibleCard/RctCollapsibleCard';
import FeedbackError from 'Components/Form/FeedbackError';
import PeopleSelect from 'Components/People/PeopleSelect';
import PropTypes from 'prop-types';
import { Editor } from 'react-draft-wysiwyg';
import { connect } from 'react-redux';
import { onChangeServiceForm } from 'Actions';
import {
    Button,
    Form,
    FormGroup,
    Label,
    Input
} from 'reactstrap';


class ServiceForm extends Component{

    constructor(props){
        super(props);
        this.state = this.initialState();
    }

    initialState = () =>{
        return {
            ...this.props.formFields,
        }
    }

    handleChange = (e, key) => {
        const value = { [key]: e.target.value };
        this.setState({...value}, () => this.props.onChangeServiceForm({...value}));
    }

    handleChangeSelect = (value, key) =>{
        const data = { [key]: value };
        this.setState({...data}, () => this.props.onChangeServiceForm({...data}));
    }

    handleChangeEditor = (value, key) =>{
        const data = { [key]: value };
        this.setState({...data}, () => this.props.onChangeServiceForm({...data}));
    }

    handleSave(){
        this.props.save();
    }

    render(){
        return(
            <div className="row justify-content-md-center">
                <div className="col-md-12">
                    <RctCollapsibleCard heading="Cadastro Requisição de Serviço">
                        <Form>
                            <div className="row justify-content-center">
                                <div className="col-sm-12 col-md-10 service-container-select">
                                    <Input type="hidden" name="id" id="id" autoComplete="off" value={this.state._id} />
                                    <div className="row mx-0 mb-3">
                                        <div className="col-7 px-0 d-flex align-items-center">
                                            <div className="mr-2">
                                                <img width="26px" src={require('../../assets/img/icons/student.png')} />
                                            </div>
                                            <div className="w-100">
                                                <h4 className="service-label text-ellipsis"> Adolescente </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <FormGroup>
                                        <PeopleSelect value={this.state.child} handleChange={(value) => this.handleChangeSelect(value, 'child')} />
                                        <FeedbackError errors={this.props.errors.child.errors} />
                                    </FormGroup>

                                    <div className="row mx-0 mb-3">
                                        <div className="col-7 px-0 d-flex align-items-center">
                                            <div className="w-100">
                                                <h4 className="service-label text-ellipsis"> Informe o Descrição</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <FormGroup>
                                        <Editor
                                            editorState={this.state.description}
                                            toolbarClassName="toolbarClassName"
                                            wrapperClassName="wrapperClassName"
                                            editorClassName="service-editor"
                                            onEditorStateChange={(e) => this.handleChangeEditor(e, 'description')}
                                            toolbar={{
                                                options: ['inline', 'fontSize', 'colorPicker', 'list', 'textAlign', 'link', 'image', 'history'],
                                                inline: { inDropdown: false },
                                                list: { inDropdown: true },
                                                textAlign: { inDropdown: true },
                                                link: { inDropdown: true },
                                                history: { inDropdown: false }
                                            }}
                                            wrapperStyle={{
                                                'border': '1px solid #C5C5C5',
                                                'borderRadius': '3px'
                                            }}
                                            toolbarStyle={{
                                                'border': 'none',
                                                'borderBottom': '1px solid #C5C5C5'
                                            }}
                                            editorStyle={{
                                                'padding': '10px'
                                            }}
                    
                                        />
                                        <FeedbackError errors={this.props.errors.description.errors} />
                                    </FormGroup>
                                </div>

                            </div>

                            <Button color="primary" onClick={ () => this.handleSave()} >Salvar</Button>
                        </Form>
                    </RctCollapsibleCard>
                </div>
            </div>
        );
    }
}

ServiceForm.propTypes = {
    save: PropTypes.func.isRequired,
    errors: PropTypes.object.isRequired
};

const mapStateToProps = ({ service }) => {
    return service;
};

export default connect(mapStateToProps, {onChangeServiceForm})(ServiceForm);