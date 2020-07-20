// require('./bootstrap');
$(document).ready(function () {
    loadApp();
});

function loadApp() {
    Chart.defaults.global.defaultFontFamily = 'Lato';
    // Chart.defaults.global.defaultFontSize = '12';
    Chart.defaults.global.defaultFontColor = '#777';

    $('.graphs').each(function () {
        loadGraph($(this).attr('id'));
    });
}

function loadGraph(id) {
    $('#' + id).html('loading...');
    $.ajax({
        url: '/chart/' + $('#' + id).attr('data-types'),
        cache: false,
        dataType: "json",
    }).done(function (data) {
        let myChart = document.getElementById(id).getContext('2d');
        let masPopChart = new Chart(myChart, data);
        // $('#' + id).html(data);
    });
}

function submitThisForm(form, target) {
    $('#submit_' + $(form).attr('id')).hide(255);
    $.ajax({
        url: '/save',
        type: "POST",
        data: $(form).serialize(),
        cache: false,
    }).done(function (data) {
        $(form).hide(255);
        loadGraph(target);
    });

    return false;
}
