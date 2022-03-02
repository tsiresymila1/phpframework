import React from 'react';
import { AuthContextProvider } from '../hooks/auth';
import {useAppSelector} from "../app/hooks";

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
    const authData = useAppSelector(state => state.auth.value)
    return <AuthContextProvider value={authData}>{children}</AuthContextProvider>;
}
