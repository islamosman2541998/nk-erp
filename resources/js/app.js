import './bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;
import toastr from 'toastr';
import Swal from 'sweetalert2';

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