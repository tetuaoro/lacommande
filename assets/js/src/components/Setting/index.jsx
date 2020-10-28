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
        forceDelivery: false,
        minPriceDelivery: 1500,
        zoneDelivery: "",
        bgImg: "",
        linkfb: "",
        linkinsta: "",
        linktwitter: "",
        label: "",
        description: "",
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
        },
        user: {
            username: "",
            email: "",
            ntahiti: "",
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
            .finally(() => { });
    }

    return (
        <Fragment>
            <Button className="btn btn-bs btn-warning mb-2" onClick={e => handleShow(setting.id)}>Modifier les informations</Button>
            <Table responsive className="table-setting">
                <tbody>
                    <tr>
                        <th>Entreprise</th>
                        <td>
                            <p><strong>nom</strong> : {setting.name}</p>
                            <p><strong>nom utilisateur</strong> : {setting.user.username}</p>
                            <p><strong>n° T.A.H.I.T.I</strong> : {setting.user.ntahiti}</p>
                            <p><strong>adresse</strong> : {setting.city}</p>
                            <hr/>
                            <p><strong>label</strong> : {setting.label}</p>
                            <p><strong>description</strong> : {setting.description}</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Image de couverture</th>
                        <td>
                            <a href={setting.bgImg} target="_blank">
                                <img src={setting.bgImg} height={200} alt="(ajouté une image de couverture)" />
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Livraison</th>
                        <td>
                            <p><strong>zone de livraison</strong> : {setting.zoneDelivery}</p>
                            <p><strong>min. de livraison</strong> : {setting.minPriceDelivery} XPF</p>
                            <p><strong>imposer la livraison</strong><i className="fas fa-info ml-2" aria-hidden="true" title="si vrai, le prix minimum sera imposé"></i> : <i className={`fas ${setting.forceDelivery ? 'fa-check text-success' : 'fa-times text-danger'}`} aria-hidden="true"></i>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>Commande</th>
                        <td>
                            <p><strong>temps min. de commande</strong> : {setting.minTimeCommand} minutes
                            </p>
                            <p><strong>auto. validation de commande</strong><i className="fas fa-info ml-2" aria-hidden="true" title="si vrai, les commandes seront validées automatiquement"></i> : <i className={`fas ${setting.autoCommandValidation ? 'fa-check text-success' : 'fa-times text-danger'}`} aria-hidden="true"></i>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>Réseaux/Contact</th>
                        <td>
                            <p><i className="fas fa-link" aria-hidden="true"></i> : {setting.bitly.link}</p>
                            <p><i className="fas fa-phone" aria-hidden="true"></i> : {setting.phone}</p>
                            <p><i className="fas fa-envelope" aria-hidden="true"></i> : {setting.user.email}</p>
                            <p><i className="fab fa-facebook-f" aria-hidden="true"></i> : {setting.linkfb}</p>
                            <p><i className="fab fa-instagram" aria-hidden="true"></i> : {setting.linkinsta}</p>
                            <p><i className="fab fa-twitter" aria-hidden="true"></i> : {setting.linktwitter}</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Horaires</th>
                        <td>
                            <ul style={{paddingLeft: '16px',}}>
                                {Object.entries(setting.openHours).map(([key, value], i) => (
                                    <li key={i}>
                                        {dayName[i]} : <span>{value.map((val, y) => (
                                            <span key={y}>{val}, </span>
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
