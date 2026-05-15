function toggleStatus(roomId, newStatus)
{
    const formData = new FormData();
    formData.append('room_id',    roomId);
    formData.append('new_status', newStatus);

    fetch('index.php?action=toggle_status', {
        method: 'POST',
        body:   formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success)
        {
            const badge = document.getElementById('badge-' + roomId);

            if (data.new_status == 'available')
            {
                badge.className = 'badge badge-available';
                badge.innerText = 'Available';
                badge.setAttribute('onclick', `toggleStatus(${roomId}, 'maintenance')`);
            }
            else
            {
                badge.className = 'badge badge-maintenance';
                badge.innerText = 'Maintenance';
                badge.setAttribute('onclick', `toggleStatus(${roomId}, 'available')`);
            }
        }
        else
        {
            alert('Failed to update status');
        }
    })
    .catch(err => {
        console.error('Toggle error:', err);
        alert('Something went wrong');
    });
}