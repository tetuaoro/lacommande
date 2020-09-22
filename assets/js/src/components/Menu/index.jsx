import React, { useContext, useEffect, useState, Fragment } from 'react';
import { Button, Table } from 'react-bootstrap';
import axios from 'axios';
import { App } from '../../stores/context';
import * as API from '../../stores/api';

export default function Menu() {

    const { setLoading, handleError, setModalContent, setModalTitle, setShow, show, content } = useContext(App);

    const [menus, setMenus] = useState([]);

    useEffect(() => {
        fetchMenus();
    }, []);

    useEffect(() => {
        const form_el = document.getElementById("menuForm");
        if (form_el) {
            $(form_el).find("#menu_meals").select2();
            $(form_el).find(".category-input").select2({
                tags: true,
                multiple: true,
                width: '100%',
                tokenSeparators: [','],
            });
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [content]);

    const handleShow = (id) => {
        if (show) {
            setShow(false);
            return;
        }
        setModalTitle(id ? "Modifier le menu" : "Ajouter un menu");
        getForm(id);
        setShow(true);
    }

    const fetchMenus = () => {
        setLoading([true, "body"]);
        fetch(API.MENUS)
            .then(res => res.json())
            .then(menus => setMenus(menus))
            .catch(err => handleError())
            .finally(() => setLoading([false, "body"]));
    }

    const getForm = (id) => {
        fetch(id ? API.MENUEDIT + id : API.MENUNEW)
            .then((response) => response.text())
            .then((form) => setModalContent(form))
            .catch(() => handleError());
    }

    const formSubmitted = (evt) => {
        evt.preventDefault();
        setLoading([true, ".modal-content"]);
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (response.status == 201 || response.status == 202) {
                    fetchMenus();
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

    const deleteMenu = (id) => {
        setLoading([true, "body"]);
        axios.delete(API.MENUDELETE + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => {
                if (response.status == 202) {
                    fetchMenus();
                }
                setShow(false);
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
            <Button onClick={() => handleShow()} title="ajouter un menu" className="mb-2 btn-bs btn-warning">Ajouter un menu</Button>
            <Table hover>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    {menus && menus.map((menu, index1) => (
                        <Fragment key={index1}>
                            <tr>
                                <th>{index1 + 1}</th>
                                <th className="text-capitalize">{menu.category.name}</th>
                                <td >
                                    <Button className="mb-2 btn-bs btn-warning" onClick={() => handleShow(menu.id)} title="modifier ce menu">
                                        <i className="fas fa-pencil-alt" aria-hidden="true"></i>
                                    </Button>
                                    <Button className="mb-2 btn-bs btn-danger" onClick={() => deleteMenu(menu.id)}>
                                        <i className="fas fa-trash" aria-hidden="true"></i>
                                    </Button>
                                </td>
                            </tr>
                            {menu.meals.map((meal, index2) => (
                                <tr key={index2}>
                                    <th className="text-center">{(index1 + 1) + "." + (index2 + 1)}</th>
                                    <td><a href={API.MEALSHOW + meal.slug + "-" + meal.id}>{meal.name}</a> </td>
                                    <td>{meal.price} XPF</td>
                                </tr>
                            )
                            )}
                        </Fragment>
                    ))}
                </tbody>
            </Table>
        </Fragment>
    )
}
