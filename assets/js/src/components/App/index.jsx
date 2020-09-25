import React, { useState, useEffect } from 'react';
import dompurify from 'dompurify';
import { Alert, Col, Container, Modal, Nav, Row, Table } from 'react-bootstrap';
import { App as AppContext } from '../../stores/context';
import Command from '../Command';
import Meal from '../Meal';
import Menu from '../Menu';
import Notification from '../Notifications';
import Subuser from '../Subuser';

import './app.css';

export default function App() {

    const sanitizer = dompurify.sanitize;

    const [component, setComponent] = useState(3);
    const [loading, setLoading] = useState([false, "body"]);
    const [error, setError] = useState("");

    const [content, setModalContent] = useState("");
    const [modalTitle, setModalTitle] = useState("");
    const [show, setShow] = useState(false);

    useEffect(() => {
        if (loading[0]) {
            spinner(loading[1], "show");
        }
        return () => {
            spinner(loading[1], "hide");
        };
    }, [loading[0]]);

    const clear = () => {
        if (show) {
            setShow(false);
        }
        if (content) {
            setModalContent(false);
        }
    }

    const handleError = () => setError("Le server ne répond pas, contacter l'adminstrateur si le problème persiste !");

    const spinner = (target, mode, alpha = 0.6) => {
        if (mode == null || mode == "show") {
            $(target).LoadingOverlay("show", {
                imageColor: appColor2,
                background: "rgba(255, 255, 255, " + alpha + ")",
            });
        } else if (mode == "hide") {
            $(target).LoadingOverlay("hide");
        }
    }

    return (
        <Container>
            {error && <Alert onClose={() => setError(false)} variant="danger" dismissible>{error}</Alert>}
            <Row>
                <Col md={2}>
                    <Table responsive="md">
                        <Nav variant="pills" className="flex-md-column d-md-ruby" defaultActiveKey={3}>
                            <Nav.Link className="btn mb-2" eventKey={1} onClick={() => setComponent(1)}>Assiettes</Nav.Link>
                            <Nav.Link className="btn mb-2" eventKey={2} onClick={() => setComponent(2)}>Carte/Menu</Nav.Link>
                            <Nav.Link className="btn mb-2" eventKey={3} onClick={() => setComponent(3)}>Mes commandes</Nav.Link>
                            <Nav.Link className="btn mb-2" eventKey={4} onClick={() => setComponent(4)}>Suppléant</Nav.Link>
                            <Nav.Link className="btn mb-2" eventKey={5} onClick={() => setComponent(5)}>Notfication</Nav.Link>
                        </Nav>
                    </Table>
                </Col>
                <Col className="user-manage">
                    <AppContext.Provider value={{ loading: loading, setLoading: setLoading, handleError: handleError, show: show, setShow: setShow, setModalContent: setModalContent, setModalTitle: setModalTitle, content: content }}>
                        {component == 1 && <Meal />}
                        {component == 2 && <Menu />}
                        {component == 3 && <Command />}
                        {component == 4 && <Subuser />}
                        {component == 5 && <Notification />}
                        <Modal show={show} onHide={clear} centered={true} scrollable={true}>
                            <Modal.Header closeButton>
                                <Modal.Title>{modalTitle}</Modal.Title>
                            </Modal.Header>
                            <Modal.Body>
                                {content && <div dangerouslySetInnerHTML={{ __html: sanitizer(content) }} />}
                            </Modal.Body>
                        </Modal>
                    </AppContext.Provider>
                </Col>
            </Row>
        </Container>
    )
}
