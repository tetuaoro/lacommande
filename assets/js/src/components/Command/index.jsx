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

function AppTable({ compare = "=", limit = 20, orderBy = "DESC" }) {

    const { setLoading, handleError, setShow, form, setForm, setModalTitle } = useContext(App);

    const [commands, setCommands] = useState([]);

    useEffect(() => {
        fetchCommands();
    }, []);

    const fetchCommands = () => {
        setLoading([true, "body"]);

        const form = new FormData();
        form.append('form[date]', new Date().toUTCString());
        form.append('form[compare]', compare);
        form.append('form[limit]', limit);
        form.append('form[order]', orderBy);

        axios.post(API.COMMANDS, form, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then(res => setCommands(res.data))
            .catch(err => handleError())
            .finally(() => setLoading([false, "body"]))
            ;
    }

    const handleInfo = (id) => {
        if (id) {
            setShow(true);
            setModalTitle("Commande Info");
            getInfo(id);
        }
    }

    const getInfo = (id) => {
        setLoading[true, ".modal-content"];
        fetch(API.COMMANDINFO + id)
            .then(res => res.text())
            .then(res => setForm(res))
            .catch(err => handleError())
            .finally(() => setLoading([false, ".modal-content"]))
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
                        <tr key={index}>
                            <th>{index + 1}</th>
                            <th className="text-capitalize">
                                {
                                    command.name + " ~ " +
                                    (compare == "=" ? new Date(command.commandAt).toLocaleTimeString() :
                                        new Date(command.commandAt).toLocaleString())
                                }
                            </th>
                            <td>
                                <Button className="btn-bs btn-warning" onClick={() => handleInfo(command.id)}>
                                    <i className="fas fa-eye" aria-hidden="true"></i>
                                </Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </Table>
        </Fragment>
    )
}
