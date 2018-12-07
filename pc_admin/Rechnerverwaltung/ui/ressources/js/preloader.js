/*
 * Preloader JS by Johannes Kreutz for PhilleConnect Backend
 */
var preloader = {
    isVisible: false,
    toggle: function(text) {
        if (this.isVisible) {
            document.getElementById('loader-background').classList.add('loader-invisible');
            setTimeout(function() {
                document.getElementById('loader-background').style.display = 'none';
            }, 550);
            this.isVisible = false;
        } else {
            document.getElementById('loader-background').style.display = 'flex';
            if (text != '') {
                document.getElementById('loader-text').innerHTML = text;
            } else {
                document.getElementById('loader-text').innerHTML = 'SPEICHERN';
            }
            setTimeout(function() {
                document.getElementById('loader-background').classList.remove('loader-invisible');
            }, 10);
            this.isVisible = true;
        }
    },
}