import React, { useContext, useEffect, useState, Fragment } from 'react';
import { Button, Nav, Table } from 'react-bootstrap';
import axios from 'axios';
import { App } from '../../stores/context';
import * as API from '../../stores/api';

export default function Command() {

    return (
        <Fragment>
            <Nav justify variant="tabs" className="my-2" role="tablist" defaultActiveKey="now">
                <Nav.Item>
                    <Nav.Link href="#yesterday" eventKey="yesterday" id="yesterday-tab" data-toggle="tab" href="#yesterday" role="tab" aria-controls="yesterday" aria-selected="true">Hier</Nav.Link>
                </Nav.Item>
                <Nav.Item>
                    <Nav.Link href="#now" eventKey="now" id="now-tab" data-toggle="tab" href="#now" role="tab" aria-controls="now" aria-selected="true">Aujourd'hui</Nav.Link>
                </Nav.Item>
                <Nav.Item>
                    <Nav.Link href="#tomorrow" eventKey="tomorrow" id="tomorrow-tab" data-toggle="tab" href="#tomorrow" role="tab" aria-controls="tomorrow" aria-selected="true">Demain</Nav.Link>
                </Nav.Item>
            </Nav>
            <div className="tab-content" id="myTabContent">
                <div className="tab-pane fade" id="yesterday" role="tabpanel" aria-labelledby="yesterday-tab">
                    <AppTable compare="<"></AppTable>
                </div>
                <div className="tab-pane fade show active" id="now" role="tabpanel" aria-labelledby="now-tab">
                    <AppTable></AppTable>
                </div>
                <div className="tab-pane fade" id="tomorrow" role="tabpanel" aria-labelledby="tomorrow-tab">
                    <AppTable compare=">"></AppTable>
                </div>
            </div>
        </Fragment>
    )
}

function AppTable({ compare = "=", orderBy = "DESC" }) {

    const { setLoading, handleError, setShow, setModalContent, content, setModalTitle } = useContext(App);

    const [commands, setCommands] = useState([]);
    const [state, setState] = useState(0);

    useEffect(() => {
        fetchCommands();
    }, []);

    useEffect(() => {
        const form_el = document.getElementById("validateForm");
        if (form_el) {
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [state]);

    const formSubmitted = (evt) => {
        evt.preventDefault();
        setLoading([true, ".modal-content"]);
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(() => {
                fetchCommands();
                setShow(false);
            })
            .catch(err => {
                if (err.response.status == 400) {
                    setModalContent(err.response.data);
                }
            })
            .finally(() => setLoading([false, ".modal-content"]));
    }

    const fetchCommands = () => {
        setLoading([true, "body"]);

        const form = new FormData();
        form.append('form[date]', new Date().toString());
        form.append('form[compare]', compare);
        form.append('form[order]', orderBy);

        axios.post(API.COMMANDS, form, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => setCommands(res.data))
            .catch(err => handleError())
            .finally(() => setLoading([false, "body"]))
            ;
    }

    const handleInfo = (id) => {
        if (id) {
            setModalTitle("Commande Info");
            setShow(true);
            getInfo(id);
        }
    }

    const handleValidate = (command, evt) => {
        evt.stopPropagation();

        if (command.id && !command.validate) {
            setModalTitle("Valider");
            setShow(true);
            getForm(command.id);
        }
    }

    const getForm = (id) => {
        fetch(API.COMMANDVALIDATE + id)
            .then((response) => response.text())
            .then((form) => {
                setModalContent(form);
                setState(state + 1);
            })
            .catch(() => handleError());
    }

    const getInfo = (id) => {
        setLoading[true, ".modal-content"];
        fetch(API.COMMANDINFO + id)
            .then(res => res.text())
            .then(info => setModalContent(info))
            .catch(err => handleError())
            .finally(() => setLoading([false, ".modal-content"]))
    }

    const bgColor = (bool) => {
        if (bool) {
            return {
                backgroundColor: 'rgba(25,255,25,0.5)'
            }
        }

        return {}
    }

    return (
        <Fragment>
            <Table hover responsive>
                <thead>
                    <tr>
                        <th scope="col" className="border-top-0">#</th>
                        <th scope="col" className="border-top-0">Nom</th>
                        <th scope="col" className="border-top-0">Action</th>
                    </tr>
                </thead>
                <tbody>
                    {commands && commands.map((command, index) => (
                        <tr key={index} onClick={() => handleInfo(command.id)} style={bgColor(command.validate)}>
                            <th>{index + 1}</th>
                            <th className="text-capitalize">
                                {
                                    command.name + " ~ " +
                                    (compare == "=" ? new Date(command.commandAt).toLocaleTimeString() :
                                        new Date(command.commandAt).toLocaleString())
                                }
                            </th>
                            <td>
                                <Button disabled={command.validate} title="valider cette commande" className="btn-bs btn-warning" onClick={(e) => handleValidate(command, e)}>
                                    <i className="fas fa-check text-success" aria-hidden="true"></i>
                                </Button>
                                <Button title="annuler cette commande" className="btn-bs btn-warning" onClick={() => handleInfo(command.id)}>
                                    <i className="fas fa-times text-danger" aria-hidden="true"></i>
                                </Button>
                                <Button title="envoyer un message" className="btn-bs btn-warning" onClick={() => handleInfo(command.id)}>
                                    <i className="fas fa-comment-dots text-secondary" aria-hidden="true"></i>
                                </Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </Table>
        </Fragment>
    )
}
