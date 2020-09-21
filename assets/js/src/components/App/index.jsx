import React, { useState, useEffect } from 'react';
import dompurify from 'dompurify';
import { Alert, Container, Modal } from 'react-bootstrap';
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
            <div className="row container-fluid">
                <div className="col-md-2">
                    <div className="table-responsive-md">
                        <div className="nav flex-md-column nav-pills d-md-ruby" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a onClick={() => setComponent(1)} className="nav-link mb-3 btn" id="v-pills-meal-tab" data-fa-icon="&#xf2e7" data-toggle="pill" href="#v-pills-meal" role="tab" aria-controls="v-pills-meal" aria-selected="true">
                                Assiettes
					        </a>
                            <a onClick={() => setComponent(2)} className="nav-link mb-3 btn" id="v-pills-menu-tab" data-fa-icon="&#xf0c9" data-toggle="pill" href="#v-pills-menu" role="tab" aria-controls="v-pills-menu" aria-selected="false">
                                Carte/Menu
					        </a>
                            <a onClick={() => setComponent(3)} className="nav-link mb-3 btn active" id="v-pills-command-tab" data-fa-icon="&#xf07a" data-toggle="pill" href="#v-pills-command" role="tab" aria-controls="v-pills-command" aria-selected="false">
                                Mes commandes
					        </a>
                            <a onClick={() => setComponent(4)} className="nav-link mb-3 btn" id="v-pills-command-tab" data-fa-icon="&#xf07a" data-toggle="pill" href="#v-pills-command" role="tab" aria-controls="v-pills-command" aria-selected="false">
                                Suppléant
					        </a>
                            <a onClick={() => setComponent(5)} className="nav-link mb-3 btn" id="v-pills-command-tab" data-fa-icon="&#xf07a" data-toggle="pill" href="#v-pills-command" role="tab" aria-controls="v-pills-command" aria-selected="false">
                                Notfication
					        </a>
                        </div>
                    </div>
                </div>
                <div className="col-md user-manage">
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
                </div>
            </div>
        </Container>
    )
}
