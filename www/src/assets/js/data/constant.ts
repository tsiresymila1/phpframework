import axios, {AxiosInstance} from "axios";

export const axiosAPI: AxiosInstance  = axios.create({
    headers: { 
        'Access-Control-Allow-Origin': '*',
        withCredentials: true,
        mode: 'no-cors'
    }
})