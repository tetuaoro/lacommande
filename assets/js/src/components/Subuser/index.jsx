import React, { useContext, useState, useEffect } from 'react';
import { Button } from 'react-bootstrap';
import { App } from '../../stores/context';
import * as API from '../../stores/api';
import axios from 'axios';

export default function Subuser() {

    const { setLoading, handleError, setShow, setModalContent, content, setModalTitle } = useContext(App);

    const handleShow = () => {
        setModalTitle("Ajouter un suppléant");
        getForm();
        setShow(true);
    }

    const getForm = () => {
        axios.get(API.SUBNEW)
            .then((response) => setModalContent(response.data))
            .catch((err) => {
                if (err.response.status == 409) {
                    handleError(err.response.data);
                } else {
                    handleError();
                }
            });
    }

    return (
        <div>
            <Button onClick={() => handleShow()} title="ajouter un suppléant" className="mb-2 btn-bs btn-warning d-none d-md-block">Ajouter un suppléant</Button>
            <Button onClick={() => handleShow()} title="ajouter un suppléant" className="btn-bs btn-warning d-md-none d-btn-fixed rounded-circle">
                <i className="fas fa-plus fa-2x" aria-hidden="true"></i>
            </Button>
        </div>
    )
}
