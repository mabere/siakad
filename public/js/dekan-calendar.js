document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: window.calendarEventsUrl || '/dekan/kegiatan/json', // Ambil dari variabel global
        eventColor: '#3788d8',
        locale: 'id',
        displayEventTime: false
    });
    calendar.render();
});