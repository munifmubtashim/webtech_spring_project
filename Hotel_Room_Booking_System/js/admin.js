document.addEventListener('DOMContentLoaded', function () {

    
    document.querySelectorAll('.btn-checkin').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-id');
            const row       = this.closest('tr');

            fetch('index.php?action=checkin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'booking_id=' + bookingId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success)
                {
                    
                    const badge = row.querySelector('.badge');
                    badge.textContent   = 'Checked-In';
                    badge.className     = 'badge badge-checkedin';

                   
                    this.textContent                    = 'Check Out';
                    this.className                      = 'btn-checkout';
                    this.setAttribute('data-id', bookingId);
                }
                else
                {
                    alert(data.message);
                }
            });
        });
    });

    
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-checkout'))
        {
            const bookingId = e.target.getAttribute('data-id');
            const row       = e.target.closest('tr');

            fetch('index.php?action=checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'booking_id=' + bookingId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success)
                {
                    const badge = row.querySelector('.badge');
                    badge.textContent = 'Checked-Out';
                    badge.className   = 'badge badge-checkedout';
                    e.target.remove();
                }
                else
                {
                    alert(data.message);
                }
            });
        }
    });

});