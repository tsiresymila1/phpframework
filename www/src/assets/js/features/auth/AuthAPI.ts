// A mock function to mimic making an async request for data
import {AxiosResponse} from 'axios';
import { AuthUserInput } from '../../types/auth';
import {axiosAPI} from "../../data/constant";
export async function postLogin(data: AuthUserInput) {
    return axiosAPI.post<Record<string, any>, AxiosResponse<any, any>, AuthUserInput>('/api/login',data,{headers: {'Content-Type': 'multipart/form-data'}});
  }
  