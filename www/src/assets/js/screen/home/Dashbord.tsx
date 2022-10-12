import React from "react";
import {Container, Breadcrumb,CardGroup } from "react-bootstrap";
import {CardStat} from "../../components/dashboard/CardStat";
export const Dashboard = () => {
    return (
        <Container fluid style={{marginTop: '20px'}}>
            <Breadcrumb
                listProps={{
                    className: "mt-2 mb-2 w-100 p-2 shadow-theme"
                }}
                className={"d-flex title-dash"}>
                <Breadcrumb.Item href="#">Admin</Breadcrumb.Item>
                <Breadcrumb.Item active>Dashboard</Breadcrumb.Item>
            </Breadcrumb>
            <div className="d-flex justify-content-around">
                <div className="text-center">
                    <CardGroup className={'justify-content-between w-100'}>
                        <CardStat title={'Etudiants'} count={'11'} link={'/admin/student'} color={'#00a65a'} />
                        <CardStat title={'Programmes'} count={'11'} link={'/admin/program'} color={'#DD4B47'} />
                        <CardStat title={'Coachs'} count={'11'} link={'/admin/coach'} color={'#f39c12'} />
                    </CardGroup>

                </div>
            </div>
        </Container>
    )
}