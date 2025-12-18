<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: '{{ $calendarUrl ?? route('dekan.kegiatan.index') }}/json',
            eventColor: '#3788d8',
            locale: 'id',
            displayEventTime: false
        });
        calendar.render();
    });
</script>