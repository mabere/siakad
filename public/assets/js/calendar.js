document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    console.log('Events Data:', window.eventsData);
    console.log('Is Admin:', window.isAdmin);
    console.log('Is Dean or Head:', window.isDeanOrHead);

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: window.eventsData,
        eventClick: function(info) {
            var props = info.event.extendedProps;
            Swal.fire({
                title: info.event.title,
                html: `
                    <p><strong>Deskripsi:</strong> ${props.description || 'Tidak ada deskripsi'}</p>
                    <p><strong>Status:</strong> ${props.status || '-'}</p>
                    <p><strong>Fakultas:</strong> ${props.faculty_name || 'Umum'}</p>
                    <p><strong>Prodi:</strong> ${props.department_name || 'Umum'}</p>
                `,
                icon: 'info',
                confirmButtonText: 'Tutup',
                showCancelButton: window.isAdmin,
                cancelButtonText: 'Edit Acara'
            }).then((result) => {
                if (result.isDismissed && window.isAdmin) {
                    openEditModal(info.event);
                }
            });
        },
        editable: window.isAdmin,
        selectable: true
    });
    calendar.render();

    var visibilitySelect = document.getElementById('visibility');
    if (visibilitySelect) {
        visibilitySelect.addEventListener('change', function() {
            document.getElementById('facultyGroup').style.display = this.value === 'faculty' ? 'block' : 'none';
            document.getElementById('departmentGroup').style.display = this.value === 'department' ? 'block' : 'none';
        });
    }

    var eventForm = document.getElementById('eventForm');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = document.getElementById('title').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const visibility = document.getElementById('visibility').value;
            const academicYearId = document.getElementById('academic_year_id').value;
            const facultyId = document.getElementById('faculty_id')?.value || '';
            const departmentId = document.getElementById('department_id')?.value || '';

            if (!title || !startDate || !endDate || !visibility || !academicYearId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Harap isi semua field yang diperlukan.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            if (window.isAdmin && visibility === 'faculty' && !facultyId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Pilih fakultas untuk visibilitas fakultas.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            if (visibility === 'department' && !departmentId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Pilih program studi untuk visibilitas prodi.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const eventId = document.getElementById('eventId').value;
            let url = eventId ? `/calendar/${eventId}` : (window.isDeanOrHead ? '/calendar/faculty/create' : '/calendar/create');
            const method = eventId ? 'PUT' : 'POST';

            const formData = new FormData(this);
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            formData.set('start_date', startDate.replace(/\.000Z$/, ''));
            formData.set('end_date', endDate.replace(/\.000Z$/, ''));

            const formDataEntries = Object.fromEntries(formData);
            console.log('Submitting to:', url);
            console.log('Form Data:', formDataEntries);
            console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').content);

            axios({
                method: 'POST',
                url: url,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response:', response);
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.data.success || 'Acara berhasil disimpan.',
                    confirmButtonText: 'OK'
                }).then(() => window.location.reload());
            })
            .catch(error => {
                console.error('Error:', error);
                console.error('Error Response:', error.response);
                let message = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan acara.';
                if (error.response?.data?.errors) {
                    message = Object.values(error.response.data.errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Acara';
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('visibility').value = window.isDeanOrHead ? 'faculty' : 'public';
    document.getElementById('facultyGroup').style.display = window.isDeanOrHead ? 'none' : (window.isAdmin && document.getElementById('visibility').value === 'faculty' ? 'block' : 'none');
    document.getElementById('departmentGroup').style.display = document.getElementById('visibility').value === 'department' ? 'block' : 'none';
    document.getElementById('eventModal').style.display = 'block';
}

function openEditModal(event) {
    document.getElementById('modalTitle').textContent = 'Edit Acara';
    document.getElementById('eventId').value = event.id;
    document.getElementById('title').value = event.title;
    document.getElementById('start_date').value = event.start.toISOString().slice(0, 19).replace('T', ' ');
    document.getElementById('end_date').value = event.end ? event.end.toISOString().slice(0, 19).replace('T', ' ') : '';
    document.getElementById('description').value = event.extendedProps.description || '';
    document.getElementById('visibility').value = event.extendedProps.visibility || 'public';
    document.getElementById('faculty_id').value = event.extendedProps.faculty_id || '';
    document.getElementById('department_id').value = event.extendedProps.department_id || '';
    document.getElementById('academic_year_id').value = event.extendedProps.academic_year_id || '';
    document.getElementById('facultyGroup').style.display = event.extendedProps.visibility === 'faculty' && !window.isDeanOrHead ? 'block' : 'none';
    document.getElementById('departmentGroup').style.display = event.extendedProps.visibility === 'department' ? 'block' : 'none';
    document.getElementById('eventModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}