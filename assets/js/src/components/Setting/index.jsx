import React, { useState, useEffect, useContext, Fragment } from 'react';
import axios from 'axios';
import { App } from '../../stores/context';
import * as API from '../../stores/api';
import { Button, Table } from 'react-bootstrap';

export default function Setting() {
    const { setLoading, handleError, setModalContent, setModalTitle, setShow, show, content } = useContext(App);
    const [dayName, setDayName] = useState([
        "Lundi",
        "Mardi",
        "Mercredi",
        "Jeudi",
        "Vendredi",
        "Samedi",
        "Dimanche",
    ]);
    const [setting, setSetting] = useState({
        id: 0,
        name: "",
        phone: "",
        slug: "",
        city: "",
        zoneDelivery: "",
        bgImg: "",
        linkfb: "",
        linkinsta: "",
        linktwitter: "",
        label: "",
        description: "",
        minPriceDelivery: 1500,
        minTimeCommand: 0,
        openHours: {
            monday: [],
            tuesday: [],
            wednesday: [],
            thursday: [],
            friday: [],
            saturday: [],
            sunday: []
        },
        bitly: {
            link: ""
        }
    });

    useEffect(() => {
        fetchSetting();
    }, []);

    useEffect(() => {
        const form_el = document.getElementById("providerForm");
        if (form_el) {
            bsCustomFileInput.init();
            autosize($('textarea'));
            form_el.addEventListener("submit", formSubmitted);
        }
    }, [content]);

    const fetchSetting = () => {
        setLoading([true, "body"]);
        fetch(API.SETTING)
            .then((response) => response.json())
            .then((setg) => setSetting(setg))
            .catch(() => handleError())
            .finally(() => setLoading([false, "body"]));
    }

    const formSubmitted = (evt) => {
        evt.preventDefault();
        setLoading([true, ".modal-content"]);
        axios.post($(evt.target).attr("action"), (new FormData(evt.target)), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (response.status == 201 || response.status == 202) {
                    fetchSetting();
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

    const handleShow = (id) => {
        if (show) {
            setShow(false);
            return;
        }
        setModalTitle("Modifier les informations");
        getForm(id);
        setShow(true);
    }

    const getForm = (id) => {
        axios.get(API.SETTINGEDIT + id)
            .then((response) => setModalContent(response.data))
            .catch((err) => {
                handleError(err.response.data.detail);
                setShow(false);
            })
            .finally(() => {});
    }

    return (
        <Fragment>
            <Button className="btn btn-bs btn-warning mb-2" onClick={e => handleShow(setting.id)}>Modifier les informations</Button>
            <Table responsive className="table-setting">
                <tbody>
                    <tr>
                        <th>Nom d'entreprise</th>
                        <td><Fragment>{setting.name}</Fragment></td>
                    </tr>
                    <tr>
                        <th>Adresse d'entreprise</th>
                        <td><Fragment>{setting.city}</Fragment></td>
                    </tr>
                    <tr>
                        <th>Zone de livraison</th>
                        <td><Fragment>{setting.zoneDelivery}</Fragment></td>
                    </tr>
                    <tr>
                        <th>Prix min. de livraison</th>
                        <td><Fragment>{setting.minPriceDelivery} XPF</Fragment></td>
                    </tr>
                    <tr>
                        <th>Temps min. de commande</th>
                        <td><Fragment>{setting.minTimeCommand} minutes</Fragment></td>
                    </tr>
                    <tr>
                        <th>Téléphone</th>
                        <td><Fragment>{setting.phone}</Fragment></td>
                    </tr>
                    <tr>
                        <th>Description/Label</th>
                        <td>{setting.description}</td>
                    </tr>
                    <tr>
                        <th>Image de couverture</th>
                        <td>
                            <a href={setting.bgImg} target="_blank">
                                <img src={setting.bgImg} width={200} height={200} alt="(IMG-BG-COVER)" />
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Réseaux/Contact</th>
                        <td>
                            <p>facebook : {setting.linkfb}</p>
                            <p>instagramm : {setting.linkinsta}</p>
                            <p>twitter : {setting.linktwitter}</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Horaires</th>
                        <td>
                            <ul>
                                {Object.entries(setting.openHours).map(([key, value], i) => (
                                    <li key={i}>
                                        {dayName[i]} : <span>{value.map((val, y) => (
                                            <span key={y}>{val ? val : "(Fermé)"}, </span>
                                        ))}</span>
                                    </li>
                                ))}
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </Table>
        </Fragment>
    )
}
