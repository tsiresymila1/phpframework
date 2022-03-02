import React from "react";
import {Route, Routes,Navigate} from "react-router-dom";
import {Login} from "./screen/Login";
import {RequireAuth} from "./components/RequireAuth";
import {Home} from "./screen/Home";
import {Dashboard} from "./screen/home/Dashbord";
import {NotFound} from "./screen/NotFound";
import {Students} from "./screen/home/Students";

function AppRouter(){
    return (<Routes>
        <Route path="/" element={<Navigate to="/admin" replace/>} />
        <Route path="/admin" element={
            <RequireAuth>
                <Home />
            </RequireAuth>
        } >
            <Route  index element={<Dashboard />} />
            <Route path="student" element={<Students />} />
            <Route path="*" element={<NotFound />} />
        </Route>
        <Route path="/login" element={<Login />} />
        <Route path="*" element={<NotFound />} />
    </Routes>)
}
export default AppRouter;