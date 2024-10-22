let timeSpent = 0;
setInterval(() => {
    timeSpent += 1;
    document.getElementById('time-spent').textContent = timeSpent;
}, 1000);
