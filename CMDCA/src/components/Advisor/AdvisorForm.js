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
    Input,
} from 'reactstrap';


export default class AdvisorForm extends Component{
    constructor(props){
        super(props);
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit=this.handleSubmit.bind(this);
        this.state={
            file:null,
            name:'',
            about:'',
            action:'',
            contact:'',
            imageURL:'',
            load:false,
        };
    };

    handleChange = (event,key) => {
        this.setState({ [key]: event.target.value });
    }
    
    handleClickTest = (event) =>{
        alert(this.state.image);
    }

    async handleSubmit(){
        this.setState({load:true});
        const data = new FormData();
        data.append('file',this.state.file);
        data.append('name',this.state.name);
        data.append('about',this.state.about);
        data.append('contact',this.state.contact);
        data.append('action',this.state.action);
        await api.post('/advisor',data).then(()=>{
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
            file:null,
            name:'',
            about:'',
            action:'',
            contact:'',
            imageURL:'',
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
                    <RctCollapsibleCard heading="Cadastro de Conselheiro">
                        <Form>
                            <Section title="Dados do conselheiro:" icon="account-box" />
                            <div className="row">
                                <div className="col-sm-12 col-md-8">
                                    <FormGroup>
                                        <Label for="name"><h4>Nome do conselheiro:</h4></Label>
                                        <Input type="text"  name="name" id="name" autoComplete="off" bsSize="lg" value={this.state.name} onChange={(e) => this.handleChange(e, 'name')}/>
                                    </FormGroup>
                                </div>
                                
                                <div className="col-sm-12 col-md-4">
                                    <Label for="advisorImage"><h4>Adicionar foto do conselheiro:</h4></Label>
                                    <input type="file" name="file" onChange={this.onChangeHandler}/>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-sm-12 col-md-6">
                                    <FormGroup>
                                        <Label for="function"><h4>Função:</h4></Label>
                                        <Input type="text"  name="function" id="function" autoComplete="off" bsSize="lg" value={this.state.action} onChange={(e) => this.handleChange(e, 'action')}/>   
                                    </FormGroup>
                                </div>
                                <div className="col-sm-12 col-md-6">
                                    <FormGroup>
                                        <Label for="contact"><h4>Contato:</h4></Label>
                                        <Input type="text" name="contact" id="contact" autoComplete="off" bsSize="lg" value={this.state.contact} onChange={(e) => this.handleChange(e, 'contact')}/>
                                    </FormGroup>
                                </div>
                            </div>

                            <div className="row">
                                <div className="col-sm-12 col-md-12">
                                        <FormGroup>
                                            <Label for="advisor-resume"><h4>Sobre o conselheiro:</h4></Label>
                                            <Input type="textarea"  name="advisor-resume" id="advisor-resume" autoComplete="off"  value={this.state.about} onChange={(e) => this.handleChange(e, 'about')}/>   
                                        </FormGroup>
                                </div>
                            </div>

                            <div className="row">
                                <div className="col-sm-12 col-md-2 p-1">
                                    <Button color="success" disabled={this.state.load} onClick={this.handleSubmit}>{this.state.load?'Cadastrando...':'Cadastrar'}</Button>
                                </div>
                                <div className="col-sm-12 col-md-2 p-1">
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
