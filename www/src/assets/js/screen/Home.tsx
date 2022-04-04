import React from 'react';
import { Outlet } from "react-router-dom";
import {Sidenav} from "../components/Sidenav";
import NavBar from "../components/NavBar";
import {Container} from "react-bootstrap";
import '../../css/responsive.css';
export const Home = () => {

    const [hideNav,setHideNav] = React.useState<boolean>(false);
    const toggle = () => {
        setHideNav(prev => !prev);
    }
    return (
        <div className={`d-flex adminWidget ${hideNav ? 'toggled' : ''}`}  id="wrapper">
            <Sidenav toggle={toggle} />
            <div id="page-content-wrapper">
                <div id="admin-content" style={{marginTop: '65px'}}>
                    <NavBar toggleSidenav={toggle} />
                    <Container fluid>
                        <Outlet />
                    </Container>
                </div>
            </div>
        </div>
    )
}