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
            <Button onClick={() => handleShow()}>Ajouter un suppléant</Button>
        </div>
    )
}
