import React from 'react';
/**
 * This provider match all props from symfony Limenius bundle, and "context" for React's component
 *
 * @returns React.Context
 */
const SFContext = React.createContext({
    context: {
        serverSide: Boolean,
        href: String,
        location: String,
        scheme: String,
        host: String,
        port: Number,
        base: String,
        pathname: String,
        search: String,
    },
    initialProps: {
        nameSite: String,
        stores: Array
    },
});

/**
 * Loading function base on spinnerLoader
 * @returns React.Context
 */
const App = React.createContext({
    loading: [],
    show: false,
    content: "",
    handleError: () => { },
    setLoading: () => { },
    setModalContent: () => { },
    setShow: () => { },
    setModalTitle: () => { }
});

export { SFContext, App };