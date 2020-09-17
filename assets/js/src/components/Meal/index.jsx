import React, { useState, useEffect, Fragment } from 'react';
import { Button, Modal, CardColumns, Card, Alert } from 'react-bootstrap';
import dompurify from 'dompurify';
import axios from 'axios';

export default function Meal() {

    const sanitizer = dompurify.sanitize;

    const [form, setForm] = useState("");
    const [show, setShow] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("")
    const [meals, setMeals] = useState({
        totalPage: 0,
        items: 0,
        page: 0,
        data: []
    });

    const fetchMeals = () => {
        setLoading(true);
        try {
            Promise.all([
                fetch("/api/manage/meals/all.json").then((response) => response.json()),
            ]).then(([meals]) => {
                setMeals(meals);
                setLoading(false);
            })
        } catch {
            console.log("data fetch error")
            setLoading(false);
            setError("Le server ne répond pas, contacter l'adminstrateur si le problème persiste !");
        }
    }

    const getForm = (id) => {
        if (id) {
            //
        } else {
            try {
                Promise.all([
                    fetch("/api/manage/new-meal").then((response) => response.text()),
                ]).then(([form]) => {
                    setForm(form);
                });
            } catch {
                console.log("new meal fetch error")
                setError("Le server ne répond pas. Contacter l'adminstrateur si le problème persiste !");
            }
        }
    }

    useEffect(() => {
        fetchMeals();
        return () => {
            setLoading(false);
        };
    }, []);

    useEffect(() => {
        const form_el = document.getElementById("mealForm");
        if (form_el) {
            $(form_el).find("[id$='description']").richTextEditor();
            $(form_el).find("[id$='recipe']").richTextEditor();
            $(form_el).find(".tags-input").select2({
                tags: true,
            });
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [form]);

    const handleClose = () => setShow(false);
    const handleShow = (id) => {
        setShow(true);
        if (id) {
            //
        } else {
            getForm();
        }
    };

    const spinner = (mode, alpha = 0.6) => {
        var spinner = $(".modal-content");
        if (mode == null || mode == "show") {
            spinner.LoadingOverlay("show", {
                imageColor: appColor2,
                background: "rgba(255, 255, 255, " + alpha + ")",
            });
        } else if (mode == "hide") {
            spinner.LoadingOverlay("hide");
        }
    }

    const formSubmitted = evt => {
        evt.preventDefault();
        spinner();
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (response.status == 201) {
                    setMeals({ data: [...meals.data, response.data] });
                    setShow(false);
                    spinner("hide");
                }
            })
            .catch(err => {
                if (err.response.status == 400 || err.response.status == 200) {
                    setForm(err.response.data);
                    spinner("hide");
                }
            });
    }

    return (
        <Fragment>
            <Button onClick={() => handleShow()} title="ajouter une assiete" className="mb-2 btn-bs btn-warning">Ajouter une assiete</Button>
            {loading &&
                <div className="text-center">
                    <div className="spinner-grow text-warning" style={{
                        width: "7rem",
                        height: "7rem"
                    }} role="status">
                        <span className="sr-only">Loading...</span>
                    </div>
                </div>
            }
            {error && <Alert variant="danger">{error}</Alert>}
            <CardColumns>
                {meals.data && meals.data.map((meal, index) => (
                    <Card key={index}>
                        <Card.Img src={meal.img} alt="IMG-MEAL" />
                        <Card.ImgOverlay className="bg-card-img-hover text-light d-flex flex-column">
                            <Card.Title>{meal.name}</Card.Title>
                            <Card.Text>{meal.createdAt}</Card.Text>
                            <div className="mt-auto">
                                <a className="btn-bs btn-warning" title="voir plus" href={`/fr/product/detail/${meal.slug}-${meal.id}`}>
                                    <i className="fas fa-eye text-light" aria-hidden="true"></i>
                                </a>
                                <Button className="btn-bs btn-warning" title="modifier" onClick={() => handleShow(meal.id)}>
                                    <i className="fas fa-pencil-alt text-light" aria-hidden="true"></i>
                                </Button>
                            </div>
                        </Card.ImgOverlay>
                    </Card>
                ))}
            </CardColumns>
            <Modal show={show} onHide={handleClose} centered={true} scrollable={true}>
                <Modal.Header closeButton>
                    <Modal.Title>Ajouter une assiete</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {form && <div dangerouslySetInnerHTML={{ __html: sanitizer(form) }} />}
                </Modal.Body>
            </Modal>
        </Fragment>
    )
}
