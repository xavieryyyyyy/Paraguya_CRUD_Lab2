// Initializes Axios and its XMLHttpRequest header for JavaScript HTTP requests. Our normal HTML
// CRUD forms do not need Axios; this remains Breeze scaffold.

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
