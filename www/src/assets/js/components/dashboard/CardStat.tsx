import React from 'react';
import {Card, Col, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import {FaUserGraduate, FaArrowRight} from 'react-icons/fa'
export const CardStat = (
    {title,count,link,color}:
        {title:string,count:string,link:string,color:string}
) =>{
    return (
        <div className={'my-5 mx-3'}>
            <Card style={{width:'350px',backgroundColor:color}} className={"border-0 shadow-theme card-radius-1"}>
                <Card.Body>
                    <Row>
                        <Col className={"justify-content-center"}>
                            <Card.Title>{title}</Card.Title>
                            <Card.Text className={"dashbord-title"}>
                                {count}
                            </Card.Text>
                        </Col>
                        <Col className={"justify-content-center"}>
                            <span className="hvr-grow">
                                <FaUserGraduate  size={40} color={'white'}/>
                            </span>
                        </Col>
                    </Row>
                </Card.Body>
                <Card.Footer>
                    <small className=" text-white">Plus d'infos<Link to={link}>
                        <span className="badge bg-theme-color badge-pill p-2 px-2">
                            <FaArrowRight  color={'white'} />
                        </span>
                    </Link>
                </small>
                </Card.Footer>
            </Card>
        </div>
    )
};