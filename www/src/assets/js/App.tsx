import React from 'react';
import { AuthProvider } from "./components/AuthProvider";
import AppRouter from "./AppRouter";
import {Provider as ReduxProvider} from "react-redux";
import {store} from "./app/store";
import {BrowserRouter} from "react-router-dom";
import '../css/App.css';
import 'bootstrap/dist/css/bootstrap.min.css';

function App() {
  return (
      <ReduxProvider store={store}>
          <BrowserRouter>
            <AuthProvider>
                <AppRouter />
            </AuthProvider>
          </BrowserRouter>
      </ReduxProvider>
  );
}

export default App;
