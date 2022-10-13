import React from "react";
import { AuthProvider } from "./components/AuthProvider";
import AppRouter from "./AppRouter";
import { Provider as ReduxProvider } from "react-redux";
import { store } from "./app/store";
import { BrowserRouter } from "react-router-dom";
import useWebSocket, { ReadyState } from "react-use-websocket";
import "../css/App.css";
import "bootstrap/dist/css/bootstrap.min.css";

function App() {
  

  React.useEffect(() => {
    let socket = new WebSocket("ws://localhost:4445");

    socket.onopen = function (e) {
      console.log("[open] Connection established");
      console.log("Sending to server");
      socket.send(JSON.stringify({event: "message", data: "Hello"})); 
    };

    socket.onmessage = function (event) {
      console.log(`[message] Data received from server: ${event.data}`);
    };

    socket.onclose = function (event) {
      if (event.wasClean) {
        console.log(
          `[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`
        );
      } else {
        // par exemple : processus serveur arrêté ou réseau en panne
        // event.code est généralement 1006 dans ce cas
        console.log("[close] Connection died");
      }
    };
    socket.onerror = function (error) {
      console.log(`[error] ${error}`);
    };
  }, []);

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
