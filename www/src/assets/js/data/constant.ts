import {Axios} from "axios";

export const axiosAPI  = new Axios({
    baseURL: "http://localhost:4444",
    headers: { 
        'Access-Control-Allow-Origin': '*',
        withCredentials: true,
        mode: 'no-cors'
    }
})