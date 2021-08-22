require ('../css/app.css');
require ('../css/sign.css');

var dateTime = document.getElementById("date-time");

function tick() {
    var d = new Date();
    var hours = d.getHours();
    var minutes = d.getMinutes();
    var ampm = 'AM';
    if (hours >= 12) {
        ampm = 'PM';
        hours = Math.abs(hours - 12);
    }
    if (minutes < 10) {
        minutes = '0' + minutes.toString();
    }
    var dateString = `${d.getMonth() + 1}/${d.getDate()}/${d.getFullYear()} ${hours}:${minutes}${ampm}`;
    dateTime.textContent = dateString;
}

window.setInterval(tick, 6000);

tick();