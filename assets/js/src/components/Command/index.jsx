import React, { useContext, useEffect, useState, Fragment } from 'react';
import { Button, Nav, Table } from 'react-bootstrap';
import axios from 'axios';
import { App, SFContext } from '../../stores/context';
import * as API from '../../stores/api';
import moment from 'moment';
import { setInterval } from 'core-js';

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

    const { setLoading, handleError, setShow, setModalContent, setModalTitle } = useContext(App);
    const { id } = useContext(SFContext).initialProps;

    const [commands, setCommands] = useState([]);
    const [state, setState] = useState(0);
    const [formData, setFData] = useState('');

    useEffect(() => {
        const form = new FormData();
        form.append('form[date]', moment().format('Y-M-D H:m:s Z'));
        form.append('form[compare]', compare);
        form.append('form[order]', compare == "<" ? orderBy : "ASC");

        setFData(form);

        var si;
        if (compare == "=") {
            si = setInterval(() => {
                fetchBackG(form, si);
            }, 30000);
        }
        fetchCommands(form, si);

        return () => {
            if (compare == "=") {
                clearInterval(si);
            }
        }
    }, []);

    useEffect(() => {
        const form_el = document.getElementById("validateForm");
        if (form_el) {
            autosize($('textarea'));
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [state]);

    const formSubmitted = (evt) => {
        evt.preventDefault();
        setLoading([true, ".modal-content"]);
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(() => {
                fetchCommands(formData);
                setShow(false);
            })
            .catch(err => {
                setModalContent(err.response.data);
                setState(state + 1);
            })
            .finally(() => setLoading([false, ".modal-content"]));
    }

    const fetchCommands = (form) => {
        setLoading([true, "body"]);
        axios.post(API.COMMANDS, form, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => setCommands(res.data))
            .catch(err => handleError())
            .finally(() => setLoading([false, "body"]))
            ;
    }

    const fetchBackG = (form, si) => {
        axios.post(API.COMMANDS, form, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => setCommands(res.data))
            .catch(err => {
                handleError();
                clearInterval(si);
            })
            ;
    }

    const handleInfo = (id) => {
        if (id) {
            setModalTitle("Commande Info");
            setShow(true);
            getInfo(id);
        }
    }

    const handleCommand = (id, bool, evt) => {
        evt.stopPropagation();
        setModalTitle(bool == 2 ? "Envoyer un message" : bool == 1 ? "Valider" : "Annuler");
        setShow(true);
        getForm(id, bool);
    }

    const getForm = (id, bool) => {
        axios.get(bool == 2 ? API.COMMANDCUSTOMMESS + id : API.COMMANDVALIDATE + id + "-" + bool)
            .then((response) => {
                setModalContent(response.data);
                setState(state + 1);
            })
            .catch((err) => {
                handleError(err.response.data.detail);
                setShow(false);
            });
    }

    const getInfo = (id) => {
        axios.get(API.COMMANDINFO + id)
            .then((response) => {
                setModalContent(response.data);
            })
            .catch((err) => {
                handleError(err.response.data.detail);
                setShow(false);
            })
            ;
    }

    const bgColor = (tab) => {
        if (tab[id] == true) {
            return {
                backgroundColor: 'rgba(25,255,25,0.5)',
            }
        } else if (tab[id] == false) {
            return {
                backgroundColor: 'rgba(255,25,0,0.5)',
            }
        } else if (Object.keys(tab).length > 0) {
            return {
                backgroundColor: 'rgba(255,190,5,0.5)',
            }
        }

        return {}
    }

    return (
        <Fragment>
            <Table hover responsive>
                <thead>
                    <tr>
                        <th scope="col" className="border-top-0"></th>
                        <th scope="col" className="border-top-0">Nom</th>
                        <th scope="col" className="border-top-0">Action</th>
                    </tr>
                </thead>
                <tbody>
                    {commands && commands.map((command, index) => (
                        <tr key={index} onClick={() => handleInfo(command.id)} style={bgColor(command.validation)}>
                            <th>{index + 1}</th>
                            <th className="text-capitalize">
                                {
                                    command.name + " ~ " +
                                    (compare == "=" ? new Date(command.commandAt).toLocaleTimeString() :
                                        new Date(command.commandAt).toLocaleString())
                                }
                            </th>
                            <td>
                                <Button disabled={command.validation[id]} title="valider cette commande" className="btn-bs btn-warning" onClick={(e) => handleCommand(command.id, 1, e)}>
                                    <i className="fas fa-check text-success" aria-hidden="true"></i>
                                </Button>
                                <Button disabled={command.validation[id] == false} title="annuler cette commande" className="btn-bs btn-warning" onClick={(e) => handleCommand(command.id, 0, e)}>
                                    <i className="fas fa-times text-danger" aria-hidden="true"></i>
                                </Button>
                                <Button title="envoyer un message" className="btn-bs btn-warning" onClick={(e) => handleCommand(command.id, 2, e)}>
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
