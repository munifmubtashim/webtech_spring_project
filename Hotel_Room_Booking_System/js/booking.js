
function searchRooms(checkin, checkout, guests) {

    const formData = new FormData();
    formData.append('checkin',  checkin);
    formData.append('checkout', checkout);
    formData.append('guests',   guests);

    fetch('index.php?action=search', {
        method: 'POST',
        body:   formData
    })
    .then(res => res.json())
    .then(rooms => {

        document.getElementById('loading').style.display = 'none';

        if (rooms.length === 0) {
            document.getElementById('noResults').style.display = 'block';
            return;
        }

        const container = document.getElementById('roomResults');

        rooms.forEach(room => {


            let amenityHTML = '';
            if (room.amenities) {
                room.amenities.forEach(a => {
                    amenityHTML += `<span class="amenity-tag">${a}</span>`;
                });
            }

            container.innerHTML += `
                <div class="room-card">
                    <div class="room-info">
                        <h3>${room.name}</h3>
                        <p>${room.description}</p>
                        <p>👥 Max Capacity: ${room.max_capacity} guests</p>
                        <div class="amenities">${amenityHTML}</div>
                    </div>
                    <div class="room-action">
                        <div class="price">
                            $${room.price_per_night}
                            <span>/ night</span>
                        </div>
                        <a href="index.php?action=book&room_type_id=${room.id}&checkin=${checkin}&checkout=${checkout}&guests=${guests}&price=${room.price_per_night}"
                           class="btn-book">
                            Book Now
                        </a>
                    </div>
                </div>
            `;
        });
    })
    .catch(err => {
        document.getElementById('loading').style.display  = 'none';
        document.getElementById('noResults').style.display = 'block';
        console.error('Search error:', err);
    });
}


function cancelBooking(bookingId) {

    if (!confirm('Are you sure you want to cancel this booking?')) return;

    const formData = new FormData();
    formData.append('booking_id', bookingId);

    fetch('index.php?action=cancel', {
        method: 'POST',
        body:   formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // update badge
            const badge       = document.getElementById('status-'     + bookingId);
            badge.className   = 'badge badge-cancelled';
            badge.innerText   = 'Cancelled';

            // disable cancel button
            const btn         = document.getElementById('cancel-btn-' + bookingId);
            btn.disabled      = true;
            btn.innerText     = 'Cancelled';

            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Something went wrong. Please try again.', 'error');
        console.error('Cancel error:', err);
    });
}


function showToast(message, type) {
    const toast            = document.getElementById('toast');
    toast.innerText        = message;
    toast.className        = 'toast toast-' + type;
    toast.style.display    = 'block';

    setTimeout(function() {
        toast.style.display = 'none';
    }, 3000);
}