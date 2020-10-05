import React, { useContext, useState, useEffect, Fragment } from "react";
import { App } from "../../stores/context";
import * as API from "../../stores/api";
import { Button, Card } from "react-bootstrap";
import axios from "axios";

export default function Notification() {
    const {
        setLoading,
        handleError,
        setModalContent,
        setModalTitle,
        setShow,
        setBadge,
    } = useContext(App);

    const [notifs, setNotifs] = useState([]);

    useEffect(() => {
        fetchNotifs();
    }, []);

    const handleShow = (notif) => {
        setModalTitle(notif.title);
        setModalContent(notif.message);
        setShow(true);
    };

    const fetchNotifs = () => {
        setLoading([true, "body"]);
        fetch(API.NOTIFICATIONS)
            .then((response) => response.json())
            .then((notifs) => setNotifs(notifs))
            .catch(() => handleError())
            .finally(() => setLoading([false, "body"]));
    };

    const deleteNotif = (id, e) => {
        e.stopPropagation();
        setLoading([true, "body"]);
        axios
            .delete(API.NOTIFICATIONDELETE + id, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
            .then((response) => {
                if (response.status == 202) {
                    fetchNotifs();
                    setBadge();
                }
            })
            .catch((err) => {
                if (err.response.status == 400) {
                    handleError();
                }
            })
            .finally(() => setLoading([false, "body"]));
    };

    const dump_html = (msg) => {
        return msg.replace(/(<([^>]+)>)/gi, "").substring(0, 33) + "...";
    };

    return (
        <Fragment>
            {notifs &&
                notifs.map((notif, index) => (
                    <Card
                        key={index}
                        onClick={() => handleShow(notif)}
                        className="shadow mb-1"
                    >
                        <Card.Header>
                            {new Date(notif.createdAt).toLocaleDateString()}
                        </Card.Header>
                        <Card.Body>
                            <Card.Subtitle className="mb-2 text-muted">
                                {notif.title}
                            </Card.Subtitle>
                            <Card.Text>{dump_html(notif.message)}</Card.Text>
                            <Button
                                variant="outline-danger"
                                className="btn-bss"
                                onClick={(e) => deleteNotif(notif.id, e)}
                            >
                                <i className="fas fa-trash" aria-hidden="true"></i>
                            </Button>
                        </Card.Body>
                    </Card>
                ))}
        </Fragment>
    );
}
