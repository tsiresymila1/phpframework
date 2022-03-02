import React from 'react';
import {Navbar, Nav, Container, Button, Image,Dropdown} from 'react-bootstrap';
import { library } from "@fortawesome/fontawesome-svg-core";
import { faBars } from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {Link} from "react-router-dom";
library.add(faBars);
function NavBar({toggleSidenav}: {toggleSidenav?: ()=>void}){
    return(
        <Navbar bg="primary" variant="light" fixed={'top'}  className={'shadow p-2 mb-5 bg-white'} style={{height: "60px"}}>
            <Button id="menu-toggle" onClick={toggleSidenav} variant="link"  ><FontAwesomeIcon icon={"bars"} className={"text-theme-color font-weight-bold"}/></Button>
            <Container fluid>
                <Navbar.Brand >CRM</Navbar.Brand>
            </Container>
            <Dropdown align="end">
                <Dropdown.Toggle  as="button" className="btn btn-link" style={{textDecoration: 'none'}} >
                    <Image src="/static/assets/profile/{{ request.session.user.image}}" width="30" height="30" className="rounded-circle" alt="Profile"/>
                </Dropdown.Toggle>
                <Dropdown.Menu className="p-0 border dropdown-menu-md-right" style={{ borderRadius:0, position:'absolute', float:'right',left : 'auto',right :'0.1em' , minWidth : '200px', }}>
                    <div className="row justify-content-center m-2">
                        <Image src="/static/assets/profile/{{ request.session.user.image}}" width="100" height="100" className="rounded-circle" alt="Profile"/>
                    </div>
                    <div className="row justify-content-center m-2">
                        <div className="text-center" style={{ fontSize: '12px'}}>
                        </div>
                    </div>
                    <div className="bg-pink m-0 text-center ">
                        <Link className="btn bg-danger btn-block text-center text-white w-100" style={{fontSize: '13px'}} to={'/admin/logout'}><i className="fa fa-arrow-left text-teal mr-2" aria-hidden="true"/> Deconnecter</Link>
                    </div>
                </Dropdown.Menu>
            </Dropdown>
        </Navbar>);
}
export default NavBar;