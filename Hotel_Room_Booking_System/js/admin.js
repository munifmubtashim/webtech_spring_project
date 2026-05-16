function checkIn(bookingId)
{
    if (!confirm('Confirm check in for booking #' + bookingId + '?')) return;

    const formData = new FormData();
    formData.append('booking_id', bookingId);

    fetch('index.php?action=checkin', {
        method: 'POST',
        body:   formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success)
        {
            
            const badge       = document.getElementById('status-' + bookingId);
            badge.className   = 'badge badge-checkedin';
            badge.innerText   = 'Checked-In';

            // swap button to checkout
            const btn         = document.getElementById('btn-' + bookingId);
            btn.className     = 'btn-checkout';
            btn.innerText     = 'Check Out';
            btn.setAttribute('onclick', 'checkOut(' + bookingId + ')');

            showToast(data.message, 'success');
        }
        else
        {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Something went wrong', 'error');
        console.error('Checkin error:', err);
    });
}

// ==================== CHECK OUT ====================
function checkOut(bookingId)
{
    if (!confirm('Confirm check out for booking #' + bookingId + '?')) return;

    const formData = new FormData();
    formData.append('booking_id', bookingId);

    fetch('index.php?action=checkout', {
        method: 'POST',
        body:   formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success)
        {
            // update status badge
            const badge       = document.getElementById('status-' + bookingId);
            badge.className   = 'badge badge-checkedout';
            badge.innerText   = 'Checked-Out';

            // remove button
            const btn         = document.getElementById('btn-' + bookingId);
            btn.outerHTML     = '—';

            showToast(data.message, 'success');
        }
        else
        {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Something went wrong', 'error');
        console.error('Checkout error:', err);
    });
}

// ==================== TOAST ====================
function showToast(message, type)
{
    const toast         = document.getElementById('toast');
    toast.innerText     = message;
    toast.className     = 'toast toast-' + type;
    toast.style.display = 'block';

    setTimeout(function() {
        toast.style.display = 'none';
    }, 3000);
}