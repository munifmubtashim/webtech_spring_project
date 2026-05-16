// ==================== CONFIRM BOOKING ====================
function confirmBooking(bookingId) {
  if (!confirm("Are you sure you want to confirm booking #" + bookingId + "?"))
    return;
 
  const formData = new FormData();
  formData.append("booking_id", bookingId);
 
  fetch("index.php?action=admin_confirm", {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      // DIAGNOSTIC CHECK: If server crashes, print the raw output text to console
      if (!res.ok) {
        return res.text().then((text) => {
          throw new Error(text);
        });
      }
      return res.json();
    })
    .then((data) => {
      if (data.success) {
        // 1. Update status badge to Confirmed
        const badge = document.getElementById("status-" + bookingId);
        badge.className = "badge badge-confirmed";
        badge.innerText = "Confirmed";
 
        // 2. Swap the action button to "Check In"
        const btn = document.getElementById("btn-" + bookingId);
        btn.className = "btn-checkin";
        btn.innerText = "Check In";
        btn.setAttribute("onclick", "checkIn(" + bookingId + ")");
 
        showToast(data.message, "success");
      } else {
        showToast(data.message, "error");
      }
    })
    .catch((err) => {
      showToast("Something went wrong", "error");
      // This will print the EXACT PHP crash message in your browser console!
      console.error("Confirmation server crash log:", err.message);
    });
}
 
// ==================== CHECK IN ====================
function checkIn(bookingId) {
  if (!confirm("Confirm check in for booking #" + bookingId + "?")) return;
 
  const formData = new FormData();
  formData.append("booking_id", bookingId);
 
  fetch("index.php?action=checkin", {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      if (!res.ok) {
        return res.text().then((text) => {
          throw new Error(text);
        });
      }
      return res.json();
    })
    .then((data) => {
      if (data.success) {
        const badge = document.getElementById("status-" + bookingId);
        badge.className = "badge badge-checkedin";
        badge.innerText = "Checked-In";
 
        // swap button to checkout
        const btn = document.getElementById("btn-" + bookingId);
        btn.className = "btn-checkout";
        btn.innerText = "Check Out";
        btn.setAttribute("onclick", "checkOut(" + bookingId + ")");
 
        showToast(data.message, "success");
      } else {
        showToast(data.message, "error");
      }
    })
    .catch((err) => {
      showToast("Something went wrong", "error");
      console.error("Checkin error:", err.message);
    });
}
 
// ==================== CHECK OUT ====================
function checkOut(bookingId) {
  if (!confirm("Confirm check out for booking #" + bookingId + "?")) return;
 
  const formData = new FormData();
  formData.append("booking_id", bookingId);
 
  fetch("index.php?action=checkout", {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      if (!res.ok) {
        return res.text().then((text) => {
          throw new Error(text);
        });
      }
      return res.json();
    })
    .then((data) => {
      if (data.success) {
        // update status badge
        const badge = document.getElementById("status-" + bookingId);
        badge.className = "badge badge-checkedout";
        badge.innerText = "Checked-Out";
 
        // remove button
        const btn = document.getElementById("btn-" + bookingId);
        btn.outerHTML = "—";
 
        showToast(data.message, "success");
      } else {
        showToast(data.message, "error");
      }
    })
    .catch((err) => {
      showToast("Something went wrong", "error");
      console.error("Checkout error:", err.message);
    });
}
 
// ==================== TOAST ====================
function showToast(message, type) {
  const toast = document.getElementById("toast");
  toast.innerText = message;
  toast.className = "toast toast-" + type;
  toast.style.display = "block";
 
  setTimeout(function () {
    toast.style.display = "none";
  }, 3000);
}