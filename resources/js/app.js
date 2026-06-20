import './bootstrap';

import 'bootstrap/dist/css/bootstrap.rtl.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

import Swal from 'sweetalert2';

import '../css/app.css';

window.toastr = toastr;
window.Swal = Swal;

toastr.options = {
    closeButton: true,
    progressBar: true,
    newestOnTop: true,
    preventDuplicates: true,
    timeOut: 3500,
    extendedTimeOut: 1500,
    positionClass: 'toast-top-left',
    rtl: true,
};