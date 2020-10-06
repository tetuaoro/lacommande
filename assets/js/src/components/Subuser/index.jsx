import React, { useContext, useState, useEffect, Fragment } from "react";
import { Badge, Button, Card, Form } from "react-bootstrap";
import { App } from "../../stores/context";
import * as API from "../../stores/api";
import axios from "axios";

export default function Subuser() {
    const {
        setLoading,
        handleError,
        setShow,
        setModalContent,
        content,
        setModalTitle,
    } = useContext(App);

    const [subs, setSubs] = useState({
        quota: 0,
        data: [],
    });

    useEffect(() => {
        fetchSubs();
    }, []);

    useEffect(() => {
        const form_el = document.getElementById("subForm");
        if (form_el) {
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [content]);

    const handleShow = (id) => {
        setModalTitle(id ? "Modifier un suppléant" : "Ajouter un suppléant");
        getForm(id);
        setShow(true);
    };

    const fetchSubs = () => {
        setLoading([true, "body"]);
        fetch(API.SUBS)
            .then((response) => response.json())
            .then((subs) => setSubs(subs))
            .catch(() => handleError())
            .finally(() => setLoading([false, "body"]));
    };

    const getForm = (id) => {
        axios
            .get(id ? API.SUBEDIT + id : API.SUBNEW)
            .then((response) => setModalContent(response.data))
            .catch((err) => {
                if (err.response.status == 409) {
                    handleError(err.response.data);
                } else {
                    handleError();
                }
                setShow(false);
            });
    };

    const formSubmitted = (evt) => {
        evt.preventDefault();
        setLoading([true, ".modal-content"]);
        axios
            .post($(evt.target).attr("action"), new FormData(evt.target), {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
            .then((response) => {
                if (response.status == 201 || response.status == 202) {
                    fetchSubs();
                }
                setShow(false);
            })
            .catch((err) => {
                if (err.response.status == 400) {
                    setModalContent(err.response.data);
                }
            })
            .finally(() => setLoading([false, ".modal-content"]));
    };

    const editSubSubmitted = (id, evt) => {
        evt.preventDefault();
        setLoading([true, $(evt.target).find("button")]);
        axios.post(API.SUBEDITAUTH + id, new FormData(evt.target), {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then((response) => {
                if (response.status == 202) {
                    fetchSubs();
                }
                setShow(false);
            })
            .catch(() => { handleError() })
            ;
    }

    const deleteSub = (id, evt) => {
        evt.stopPropagation();
        setLoading([true, evt.target]);
        axios.delete(API.SUBDELETE + id, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then((response) => {
                if (response.status == 202) {
                    fetchSubs();
                }
            })
            .catch(() => { handleError() })
            ;
    }

    return (
        <Fragment>
            {subs.data.length < subs.quota && (
                <Fragment>
                    <Button onClick={() => handleShow()} title="ajouter un suppléant" className="mb-2 btn-bs btn-warning d-none d-md-inline-block">
                        Ajouter un suppléant
                    </Button>
                    <Button
                        onClick={() => handleShow()}
                        title="ajouter un suppléant"
                        className="btn-bs btn-warning bg-warning text-light d-md-none d-btn-fixed rounded-circle"
                    >
                        <i className="fas fa-plus fa-2x" aria-hidden="true"></i>
                    </Button>
                </Fragment>
            )}
            <h3 className="ml-1 d-inline-block align-top">
                <Badge variant="info">
                    Quota {subs.data.length}/{subs.quota}
                </Badge>
            </h3>
            <Fragment>
                {subs &&
                    subs.data.map((sub, index) => (
                        <Card key={index} className="shadow mb-1" onClick={() => handleShow(sub.id)}>
                            <Card.Header>
                                {sub.name}
                                <Button onClick={(e) => deleteSub(sub.id, e)} variant="danger" className="btn-bss float-right">
                                    <i className="fas fa-trash" aria-hidden="true"></i>
                                </Button>
                            </Card.Header>
                            <Card.Body>
                                <Fragment>
                                    <h6>Autorisation</h6>
                                    <Form name="form" inline onSubmit={(e) => editSubSubmitted(sub.id, e)}>
                                        <Form.Check onClick={(e) => e.stopPropagation()} label="Voir les commandes" className="ml-md-2" disabled checked type="checkbox" id="command" />

                                        <Form.Check onClick={(e) => e.stopPropagation()} id={`ec${sub.id}`} name="form[command]" className="ml-md-2" defaultChecked={sub.roles['command-crud']} type="checkbox" id="command-crud" title="Répondre/Valider/Refuser les commandes" />
                                        <Form.Label onClick={(e) => e.stopPropagation()} htmlFor={`ec${sub.id}`}>Editer les commandes</Form.Label>

                                        <Form.Check onClick={(e) => e.stopPropagation()} id={`em${sub.id}`} name="form[meal]" className="ml-md-2" defaultChecked={sub.roles['meal-crud']} type="checkbox" id="meal-crud" title="Créer/Modifer les produits" />
                                        <Form.Label onClick={(e) => e.stopPropagation()} htmlFor={`em${sub.id}`}>Editer les produits</Form.Label>

                                        <Button onClick={(e) => e.stopPropagation()} type="submit" className="ml-md-2 btn-bss">
                                            Valider
                                        </Button>
                                        <input type="hidden" name="form[put]" value="1" />

                                    </Form>
                                </Fragment>
                            </Card.Body>
                        </Card>
                    ))}
            </Fragment>
        </Fragment>
    );
}
