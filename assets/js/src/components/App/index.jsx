import React, { useContext, useState, useEffect } from 'react';
import { SFContext } from '../../routes/context';

export default function App() {

    const { initialProps, context } = useContext(SFContext);
    const [commands, setCommands] = useState(initialProps.stores);

    useEffect(() => {
        setTimeout(() => {
            checkCommandUpdate();
        }, 10000);
    }, [commands]);

    const checkCommandUpdate = () => {
        (async () => {
            try {
                await Promise.all([
                    fetch("/api/command/all.json").then((response) => response.json()),
                ]).then(([commands]) => {
                    setCommands(commands);
                })
            } catch {
                console.log("data fetch error")
            }
        })()
    }

    const bColor = (date) => {
        const today = new Date();
        const _date = new Date(date);

        if (today.toDateString() == _date.toDateString()) {
            // mauve = aujourd'hui
            return "rgba(92, 0, 255, 0.5)";
        }

        if (today < _date) {
            // jaune = demain
            return "rgba(255, 215, 65, 0.5)";
        } else if (today > _date) {
            // rouge = hier
            return "rgba(215, 158, 137, 0.5)";
        }
    }

    return (
        <div>
            <h1>La commande</h1>
            <div className="table-responsive">
                <table className="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">REF</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Date</th>
                            <th scope="col">Adresse</th>
                            <th scope="col">Email</th>
                            <th scope="col">Tel</th>
                            <th scope="col">Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                        {commands && commands.map((command, index) => (
                            <tr key={index} style={{
                                backgroundColor: bColor(command.commandAt)
                            }}>
                                <th scope="row">{command.reference}</th>
                                <td>{command.name}</td>
                                <td>{new Date(command.commandAt).toLocaleString()}</td>
                                <td>{command.address}</td>
                                <td>
                                    <a href={`mailto:${command.email}`}>{command.email}</a>
                                </td>
                                <td>
                                    <a href={`tel:${command.phone}`}>{command.phone}</a>
                                </td>
                                <td>{command.price}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    )
}
