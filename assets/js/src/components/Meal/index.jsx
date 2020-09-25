import React, { useState, useEffect, Fragment, useContext } from 'react';
import { Button, Modal, CardColumns, Card, Pagination } from 'react-bootstrap';
import axios from 'axios';
import { App } from '../../stores/context';
import * as API from '../../stores/api';

export default function Meal() {

    const { setLoading, handleError, setModalContent, setModalTitle, setShow, show, content } = useContext(App);

    const [pagination, setPagination] = useState("?page=1");
    const [meals, setMeals] = useState({
        totalPage: 0,
        items: 0,
        page: 0,
        data: []
    });

    useEffect(() => {
        fetchMeals();
        return () => {
            setLoading([false, "body"]);
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
                width: '100%',
                tokenSeparators: [','],
            });
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [content]);

    const handlePage = (page) => setPagination(`?page=${page}`);
    const handleShow = (id) => {
        if (show) {
            setShow(false);
            return;
        }
        setModalTitle(id ? "Modifier une assiette" : "Ajouter une assiette");
        getForm(id);
        setShow(true);
    }

    const fetchMeals = () => {
        setLoading([true, "body"]);
        fetch(API.MEALS + pagination)
            .then((response) => response.json())
            .then((meals) => setMeals(meals))
            .catch(() => handleError())
            .finally(() => setLoading([false, "body"]));
    }

    const getForm = (id) => {
        fetch(id ? API.MEALEDIT + id : API.MEALNEW)
            .then((response) => response.text())
            .then((form) => setModalContent(form))
            .catch(() => handleError());
    }

    const formSubmitted = (evt) => {
        evt.preventDefault();
        setLoading([true, ".modal-content"]);
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (response.status == 201) {
                    setMeals({
                        ...meals,
                        data: [...meals.data, response.data]
                    });
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
                    setModalContent(err.response.data);
                }
            })
            .finally(() => setLoading([false, ".modal-content"]));
    }

    const deleteMeal = (id) => {
        setLoading([true, "body"]);
        axios.delete(`${API.MEALDELETE}${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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
                    handleError();
                }
            })
            .finally(() => setLoading([false, "body"]));
    }

    return (
        <Fragment>
            <Button onClick={() => handleShow()} title="ajouter une assiete" className="mb-2 btn-bs btn-warning">Ajouter une assiete</Button>
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
                                    <a className="btn-bs btn-warning" title="voir plus" href={API.MEALSHOW + meal.slug + '-' + meal.id}>
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
        </Fragment>
    )
}
