import React, { useState, useEffect, Fragment } from 'react';
import { Button, Modal, CardColumns, Card, Alert, Pagination, Row, Col } from 'react-bootstrap';
import dompurify from 'dompurify';
import axios from 'axios';

export default function Meal() {

    const sanitizer = dompurify.sanitize;

    const [form, setForm] = useState("");
    const [show, setShow] = useState(false);
    const [pagination, setPagination] = useState("?page=1");
    const [modalTitle, setModalTitle] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const [meals, setMeals] = useState({
        totalPage: 0,
        items: 0,
        page: 0,
        data: []
    });

    const fetchMeals = () => {
        setLoading(true);
        fetch(`/api/manage/meals${pagination}`)
            .then((response) => response.json())
            .then((meals) => setMeals(meals))
            .catch(() => setError("Le server ne répond pas, contacter l'adminstrateur si le problème persiste !"))
            .finally(() => setLoading(false));
    }

    const getForm = (id) => {
        fetch(id ? `/api/manage/edit-meal-${id}` : "/api/manage/new-meal")
            .then((response) => response.text())
            .then(form => setForm(form))
            .catch(() => setError("Le server ne répond pas. Contacter l'adminstrateur si le problème persiste !"))
    }

    useEffect(() => {
        fetchMeals();
        return () => {
            setLoading(false);
        };
    }, [pagination]);

    useEffect(() => {
        const form_el = document.getElementById("mealForm");
        if (form_el) {
            bsCustomFileInput.init();
            $(form_el).find("[id$='description']").richTextEditor();
            $(form_el).find("[id$='recipe']").richTextEditor();
            $(form_el).find(".tags-input").select2({
                tags: true,
            });
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [form]);

    useEffect(() => {
        if (loading) {
            spinner("body", "show");
        }
        return () => {
            spinner("body", "hide");
        };
    }, [loading]);

    const handlePage = (page) => {
        setPagination(`?page=${page}`);
    };
    const handleClose = () => setShow(false);
    const handleShow = (id) => {
        setShow(true);
        setModalTitle(id ? "Modifier une assiette" : "Ajouter une assiette");
        getForm(id);
    };

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

    const formSubmitted = (evt) => {
        evt.preventDefault();
        spinner(".modal-content");
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (response.status == 201) {
                    setMeals({ data: [...meals.data, response.data] });
                }
                if (response.status == 202) {
                    meals.data.splice((meals.data.findIndex(meal => meal.id == response.data.id)), 1, response.data);
                    setMeals({
                        ...meals,
                        data: [...meals.data]
                    });
                }
                setShow(false);
            })
            .catch(err => {
                if (err.response.status == 400) {
                    setForm(err.response.data);
                }
            })
            .finally(() => spinner(".modal-content", "hide"));
    }

    const deleteMeal = (id) => {
        setLoading(true);
        axios.delete(`/api/manage/delete-meal-${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => {
                if (response.status == 202) {
                    meals.data.splice((meals.data.findIndex(meal => meal.id == id)), 1);
                    setMeals({
                        ...meals,
                        data: [...meals.data]
                    });
                }
            })
            .catch((err) => {
                if (err.response.status == 400) {
                    setError("Vous ne pouvez pas supprimer cette assiette.");
                }
            })
            .finally(() => setLoading(false));
    }

    return (
        <Fragment>
            <Button onClick={() => handleShow()} title="ajouter une assiete" className="mb-2 btn-bs btn-warning">Ajouter une assiete</Button>
            {error && <Alert onClose={() => setError(false)} variant="danger" dismissible>{error}</Alert>}
            <Fragment>
                {meals.page > 0 &&
                    <Pagination className="d-flex justify-content-center mb-2">
                        {meals.page > 1 &&
                            <Fragment>
                                {meals.page > 2 &&
                                    <Fragment>
                                        <Pagination.Prev onClick={() => handlePage(meals.page - 1)} title="précédent" />
                                        <Pagination.Item onClick={() => handlePage(1)}>1</Pagination.Item>
                                        {meals.page != 3 && <Pagination.Ellipsis disabled />}
                                    </Fragment>
                                }
                                <Pagination.Item onClick={() => handlePage(meals.page - 1)}>{meals.page - 1}</Pagination.Item>
                            </Fragment>
                        }
                        <Pagination.Item active>{meals.page}</Pagination.Item>
                        {meals.totalPage > 1 &&
                            <Fragment>
                                {meals.page != meals.totalPage && <Pagination.Item onClick={() => handlePage(meals.page + 1)}>{meals.page + 1}</Pagination.Item>}
                                {meals.page < meals.totalPage - 1 &&
                                    <Fragment>
                                        {meals.page < meals.totalPage - 2 && <Pagination.Ellipsis disabled />}
                                        <Pagination.Item onClick={() => handlePage(meals.totalPage)}>{meals.totalPage}</Pagination.Item>
                                        <Pagination.Next onClick={() => handlePage(meals.page + 1)} title="suivant" />
                                    </Fragment>
                                }
                            </Fragment>
                        }
                    </Pagination>
                }
                <CardColumns>
                    {meals.data && meals.data.map((meal, index) => (
                        <Card key={index}>
                            <Card.Img src={meal.img} alt="IMG-MEAL" width={200} height={200} style={{ imageRendering: "pixelated" }} />
                            <Card.ImgOverlay className="bg-card-img-hover text-light d-flex flex-column">
                                <Card.Title>{meal.name}</Card.Title>
                                <Card.Text>{new Date(meal.createdAt).toLocaleDateString()}</Card.Text>
                                <div className="mt-auto">
                                    <a className="btn-bs btn-warning" title="voir plus" href={`/fr/product/details/${meal.slug}-${meal.id}`}>
                                        <i className="fas fa-eye text-light" aria-hidden="true"></i>
                                    </a>
                                    <Button className="btn-bs btn-warning" title="modifier cette assiette" onClick={() => handleShow(meal.id)}>
                                        <i className="fas fa-pencil-alt text-light" aria-hidden="true"></i>
                                    </Button>
                                    <Button className="btn-bs btn-warning" title="supprimer cette assiette" onClick={() => deleteMeal(meal.id)}>
                                        <i className="fas fa-trash text-light" aria-hidden="true"></i>
                                    </Button>
                                </div>
                            </Card.ImgOverlay>
                        </Card>
                    ))}
                </CardColumns>
            </Fragment>
            <Modal id="mealModal" show={show} onHide={handleClose} centered={true} scrollable={true}>
                <Modal.Header closeButton>
                    <Modal.Title>{modalTitle}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {form && <div dangerouslySetInnerHTML={{ __html: sanitizer(form) }} />}
                </Modal.Body>
            </Modal>
        </Fragment>
    )
}
