import React from 'react';
import ReactDOM from 'react-dom';

import "../css/app.css";
const HelloWorld = () => {
    return (
        <h1 className="red">
            Hello Mila
        </h1>
    );
}

ReactDOM.render(<HelloWorld />, document.getElementById("root"));