document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time-slot');
    const btnSubmit = document.getElementById('btn-submit');
    const messageDiv = document.getElementById('message');

    function loadTimeslots() {
        const serviceId = serviceSelect.value;
        const date = dateInput.value;
        if (!serviceId || !date) {
            timeSelect.innerHTML = '<option value="">-- válassz --</option>';
            return;
        }
        fetch('ajax/get_timeslots.php?service_id=' + serviceId + '&date=' + date)
            .then(response => response.json())
            .then(data => {
                let html = '<option value="">-- válassz időpontot --</option>';
                data.timeslots.forEach(ts => {
                    html += `<option value="${ts}">${ts}</option>`;
                });
                timeSelect.innerHTML = html;
            });
    }

    serviceSelect.addEventListener('change', loadTimeslots);
    dateInput.addEventListener('change', loadTimeslots);

    btnSubmit.addEventListener('click', () => {
        const formData = new FormData(document.getElementById('booking-form'));
        fetch('ajax/make_booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(obj => {
            if (obj.success) {
                messageDiv.innerHTML = '<p style="color: green;">' + obj.message + '</p>';
            } else {
                messageDiv.innerHTML = '<p style="color: red;">' + obj.message + '</p>';
            }
        })
        .catch(err => {
            messageDiv.innerHTML = '<p style="color: red;">Hiba történt: ' + err + '</p>';
        });
    });
});
