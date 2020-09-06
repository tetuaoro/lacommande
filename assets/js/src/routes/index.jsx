import React from 'react';
import { renderToString } from 'react-dom/server';
import { SFContext } from './context';
import App from '../components/App';

/**
 *
 *
 * @export
 * @param {*} props
 * @param {*} railsContext
 * @returns
 */
export default function Entry(props, railsContext) {


    if (railsContext.serverSide) {
        return {
            renderedHtml: renderToString(
                <SFContext.Provider value={{ initialProps: props, context: railsContext }}>
                    <App />
                </SFContext.Provider>
            )
        };
    } else {
        return (
            <SFContext.Provider value={{ initialProps: props, context: railsContext }}>
                <App />
            </SFContext.Provider>
        );
    }
}
