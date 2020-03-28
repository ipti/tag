import React, {Component} from 'react';
import RctCollapsibleCard from 'Components/RctCollapsibleCard/RctCollapsibleCard';
import Section from 'Components/Form/Section';
import api from '../../api/multpart.js';
import successalert from '../../util/successalert';
import catcherror from '../../util/catcherror';

import {
	Button,
	Form,
	FormGroup,
	Label,
	Input
} from 'reactstrap';


export default class ResolutionForm extends Component{
    constructor(props){
        super(props);
        this.handleChange=this.handleChange.bind(this);
        this.handleSubmit=this.handleSubmit.bind(this);
        this.state={
            title:'',
            responsable:'',
            office:'',
            file:'',
            filename:'',
            load:false,
        };
    };

    handleChange = (event,key) => {
        this.setState({ [key]: event.target.value });
    }

    handleChangeFile = (event) => {
        this.setState({file: event.target.files});
    }
    
    handleClickTest = (event) =>{
        alert(this.state.file);
    }

     async handleSubmit(){
        this.setState({load:true});
        const data = new FormData();
        data.append('file',this.state.file);
        data.append('title',this.state.title);
        data.append('responsable',this.state.responsable);
        data.append('office',this.state.office);
        await api.post('/resolution',data).then(()=>{
            successalert();
          this.clearState();
        })
        .catch((error)=>{
            catcherror(error);
            this.setState({
                load:false
            });
        })
        
      }

    clearState = () =>{
        this.setState({
            title:'',
            responsable:'',
            office:'',
            filename:'',
            file:'',
            load:false,
        });
    }

    onChangeHandler=(event)=>{
        let file = event.target.files[0];
        this.setState({
            file: file
        });
        
    }

    render(){
        return(
            <div className="row justify-content-md-center">
                <div className="col-md-12">
                    <RctCollapsibleCard heading="Cadastro de resolução">
                        <Form>
                            <Section title="Dados da Resolução:" icon="collection-text" />
                            <div className="row">
                                <div className="col-sm-12 col-md-8">
                                    <FormGroup>
                                        <Label for="title">Título da resolução:</Label>
                                        <Input type="text"  name="title" id="title" autoComplete="off" bsSize="lg" value={this.state.title} onChange={(e) => this.handleChange(e, 'title')}/>
                                    </FormGroup>
                                </div>
                                <div className="col-sm-12 col-md-4">
                                    <Label for="advisorImage"><h4>Selecionar arquivo da resolução:</h4></Label>
                                    {/*<Input type="file" name="advisorImage" id="advisorImage" value={this.state.image} onChange={(e) => this.handleChange(e, 'image')}/>*/}
                                    <input type="file" name="file" placeholder="Selecione um documento" onChange={this.onChangeHandler}/>
                                </div>
                            </div>    
                            
                            <Section title="Responsável:" icon="account-box" />
                            <div className="row">
                                <div className="col-sm-12 col-md-6">
                                    <FormGroup>
                                        <Label for="person-responsable">Responsável pela assinatura:</Label>
                                        <Input type="text"  name="person-responsable" id="person-responsable" autoComplete="off" bsSize="lg" value={this.state.responsable} onChange={(e) => this.handleChange(e, 'responsable')}/>
                                        
                                    </FormGroup>
                                </div>
                                <div className="col-sm-12 col-md-6">
                                    <FormGroup>
                                        <Label for="number">Cargo:</Label>
                                        <Input type="text" name="number" id="number" autoComplete="off" bsSize="lg" value={this.state.office} onChange={(e) => this.handleChange(e, 'office')}/>
                                    </FormGroup>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-12 col-md-2">
                                    <Button color="success" disabled={this.state.load} onClick={this.handleSubmit}>{this.state.load?'Publicando...':'Publicar'}</Button>
                                </div>
                                <div className="col-sm-12 col-md-2">
                                    <Button color="orange" disabled={this.state.load} onClick={this.clearState.bind(this)}>Limpar</Button>
                                </div>
                            </div>
                        </Form>
                    </RctCollapsibleCard>
                </div>
            </div>
        );
    }
}