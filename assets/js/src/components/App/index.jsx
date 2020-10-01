import React, { useState, useEffect } from 'react';
import dompurify from 'dompurify';
import { Alert, Col, Container, Modal, Nav, Row, Table } from 'react-bootstrap';
import { App as AppContext } from '../../stores/context';
import Command from '../Command';
import Meal from '../Meal';
import Menu from '../Menu';
import Notification from '../Notifications';
import Subuser from '../Subuser';

import './entry.css';

export default function App() {

    const sanitizer = dompurify.sanitize;

    const [component, setComponent] = useState(2);
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
    }, [loading]);

    useEffect(() => {
        if (error) {
            setError(false);
        }
    }, [content]);

    const clear = () => {
        if (show) {
            setShow(false);
        }
        if (content) {
            setModalContent(false);
        }
    }

    const handleComponent = (number) => {
        if (error) {
            setError(false);
        }
        setComponent(number);
    }

    const handleError = (msg) => setError(msg ? msg : "Le server ne répond pas, contacter l'adminstrateur si le problème persiste !");

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
                        <Nav variant="pills" className="flex-md-column d-md-ruby" defaultActiveKey={2}>
                            {["Assiettes", "Carte/Menu", "Mes commandes", "Suppléant", "Notfication"].map((name, index) => (
                                <Nav.Link key={index} className="btn mb-2" eventKey={index} onClick={() => handleComponent(index)}>{name}</Nav.Link>
                            ))}
                        </Nav>
                    </Table>
                </Col>
                <Col className="user-manage">
                    <AppContext.Provider value={{
                        show: show,
                        loading: loading,
                        setShow: setShow,
                        setLoading: setLoading,
                        handleError: handleError,
                        setModalTitle: setModalTitle,
                        setModalContent: setModalContent,
                        content: content
                    }}>
                        {component == 0 && <Meal />}
                        {component == 1 && <Menu />}
                        {component == 2 && <Command />}
                        {component == 3 && <Subuser />}
                        {component == 4 && <Notification />}
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
