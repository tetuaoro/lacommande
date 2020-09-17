import React, { useState } from 'react';
import Command from '../Command';
import Meal from '../Meal';
import Menu from '../Menu';

export default function App() {

    const [component, setComponent] = useState(1);

    return (
        <div className="row container-fluid">
            <div className="col-md-2">
                <div className="nav flex-md-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a onClick={() => {setComponent(1)}} className="nav-link mb-3 btn" id="v-pills-meal-tab" data-fa-icon="&#xf2e7" data-toggle="pill" href="#v-pills-meal" role="tab" aria-controls="v-pills-meal" aria-selected="true">
                        Assiettes
					</a>
                    <a onClick={() => {setComponent(2)}} className="nav-link mb-3 btn" id="v-pills-menu-tab" data-fa-icon="&#xf0c9" data-toggle="pill" href="#v-pills-menu" role="tab" aria-controls="v-pills-menu" aria-selected="false">
                        Carte/Menu
					</a>
                    <a onClick={() => {setComponent(3)}} className="nav-link mb-3 btn" id="v-pills-command-tab" data-fa-icon="&#xf07a" data-toggle="pill" href="#v-pills-command" role="tab" aria-controls="v-pills-command" aria-selected="false">
                        Mes commandes
					</a>
                </div>
            </div>
            <div className="col-md user-manage">
                {component == 1 && <Meal />}
                {component == 2 && <Menu />}
                {component == 3 && <Command />}
            </div>
        </div>
    )
}
