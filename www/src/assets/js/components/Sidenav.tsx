import React from 'react';
import { Image, Row,Col,ListGroup } from 'react-bootstrap';
import { Link,useLocation } from "react-router-dom"; 
import * as fontawesome from "@fortawesome/fontawesome";
import {faUsers, faServer,faCreditCard,faFileAlt,faMap, faUser,faFolderOpen,faChalkboardTeacher} from "@fortawesome/free-solid-svg-icons";
import logo from '../images/logo.jpg';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
fontawesome.library.add(faUsers, faServer,faCreditCard,faFileAlt,faMap, faUser,faFolderOpen,faChalkboardTeacher);
export const Sidenav = ({toggle}:{toggle?:()=>void}) => {
    const classList = "p-3 list-group-item list-group-item-action mb-1";
    const location = useLocation();
    React.useEffect(()=>{
        if(toggle && window.innerWidth <= 768) toggle();
    },[location])
    return (
        <div className="border-right bg-white" id="sidebar-wrapper" style={{ marginTop: '60px'}}>
            <div className="sidebar-heading primary-white "> 
                <Row>
                    <Col xs={12} className=" mt-4" style={{height:'150px'}} >
                        <Image src={logo} className="rounded-circle img-fluid" width="150" height="80" alt="" />
                    </Col>
                </Row> 
            </div>
            <ListGroup variant="flush" as={"div"}>
                <Link to="/admin" className={`${classList} ${location.pathname === '/admin' ? 'active' : ''}`} >
                    <FontAwesomeIcon icon={faServer} />
                    <span className="ml-4"> Dashboard</span>
                </Link>
                <Link to="/admin/student" className={`${classList}  ${location.pathname === '/admin/student' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faUsers} />
                    <span className="ml-4" > Etudiants</span>
                </Link>
                <Link to="/admin/transcription" className={`${classList}  ${location.pathname === '/admin/transcription' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faFileAlt} />
                    <span className="ml-4" > Notes</span>
                </Link>
                <Link to="/admin/fee" className={`${classList}  ${location.pathname === '/admin/fee' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faCreditCard} />
                    <span className="ml-4" > Cotisation</span>
                </Link>
                <Link to="/admin/program" className={`${classList}  ${location.pathname === '/admin/program' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faMap} />
                    <span className="ml-4" > Programmes</span>
                </Link>
                <Link to="/admin/account" className={`${classList}  ${location.pathname === '/admin/account' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faUser} />
                    <span className="ml-4" > Compte</span>
                </Link>
                <Link to="/admin/document" className={`${classList}  ${location.pathname === '/admin/document' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faFolderOpen} />
                    <span className="ml-4" > Documents</span>
                </Link>
                <Link to="/admin/coach" className={`${classList}  ${location.pathname === '/admin/coach' ? 'active' : ''}`}>
                    <FontAwesomeIcon icon={faChalkboardTeacher} />
                    <span className="ml-4" > Coachs</span>
                </Link>
            </ListGroup>
        </div>

    )
}