import React, { useContext, useState } from 'react';
import { SFContext } from '../../routes/context';

export default function App() {

    const {initialProps, context} = useContext(SFContext);
    const [state, setstate] = useState(initialProps.stores);

    return (
        <div>
            <h1>App component</h1>
            <ul>

                {state && state.map((command, index) => (
                    <li key={index}>{command.reference} : {command.name} à {command.createdAt}</li>
                ))}
                
            </ul>

        </div>
    )
}
