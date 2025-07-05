import axios from "axios";
import { route } from "ziggy-js";

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            window.location.href = route("frontend.login");
        }
        return Promise.reject(error);
    },
);
