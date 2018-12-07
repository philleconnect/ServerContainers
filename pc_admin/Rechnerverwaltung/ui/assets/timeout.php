<p id="timeout"></p>
<script>
    var timeinterval = null;
    function getTimeRemaining(endtime) {
        var t = Date.parse(endtime) - Date.parse(new Date());
        var seconds = ('0' + (Math.floor((t / 1000) % 60))).substr(-2);
        var minutes = Math.floor((t / 1000 / 60) % 60);
        var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
        var days = Math.floor(t / (1000 * 60 * 60 * 24));
        return {
            'total': t,
            'days': days,
            'hours': hours,
            'minutes': minutes,
            'seconds': seconds
        };
    }
    function initializeClock(id, endtime) {
        var clock = document.getElementById(id);
        function updateClock() {
            var t = getTimeRemaining(endtime);
            clock.innerHTML = 'Auto-Logout in '+t.minutes+':'+t.seconds;
            if (t.total <= 0) {
                clearInterval(timeinterval);
                window.location.href = 'logout.php';
            }
        }
        updateClock();
        timeinterval = setInterval(updateClock, 1000);
    }
    function resetClock() {
        clearInterval(timeinterval);
        initializeClock('timeout', new Date((Date.parse(new Date) + 1200000)));
    }
    function pauseClock() {
        clearInterval(timeinterval);
        document.getElementById('timeout').innerHTML = 'Auto-Logout ausgesetzt';
    }
    var deadline = new Date('<?php echo date('c', ($_SESSION['timeout']+1200)); ?>');
    initializeClock('timeout', deadline);
</script>