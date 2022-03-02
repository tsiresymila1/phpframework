import { createAsyncThunk, createSlice, PayloadAction } from '@reduxjs/toolkit';
import { RootState } from '../app/store'
import {postLogin} from "../features/auth/AuthAPI";
import {AuthUserInput} from "../types/auth";

export interface AuthState {
    value: Record<string, any>;
    status: 'idle' | 'loading' | 'failed';
}
const userDataLocal = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
const initialState: AuthState = {
    value: userDataLocal ?? {
        error: null,
        auth: false
    } ,
    status: 'idle',
};

export const postLoginAsync = createAsyncThunk(
    'auth/login',
    async (data: AuthUserInput) => {
        const response = await postLogin(data);
        return response.data;
    }
);

export const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(postLoginAsync.pending, (state) => {
                state.status = 'loading';
            })
            .addCase(postLoginAsync.rejected, (state) => {
                state.status = 'failed';
            })
            .addCase(postLoginAsync.fulfilled, (state, action) => {
                state.status = 'idle';
                state.value = action.payload;
                localStorage.setItem('user',JSON.stringify(action.payload,undefined,2))
            });
    },
});

export const selectCount = (state: RootState) => state.auth.value;
export default authSlice.reducer
