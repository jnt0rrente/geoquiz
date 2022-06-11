class QuizListManager {

    constructor() {
        this.apikey = "e1d215c28c8c49389af3cc37b829c5f5";
        this.url = "https://api.opencagedata.com/geocode/v1/json";
    }

    buildUrl(lat, long) {
        let returnUrl = this.url +
            "?key=" + this.apikey +
            "&q=" + encodeURIComponent(lat + "," + long);

        return returnUrl;
    }

    loadContinent() {
        let url = this.buildUrl(this.latitude, this.longitude);
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: this.callSuccess,
            error: this.callError
        });
    }

    callSuccess(data) {
        let continent = data.results[0].components.continent;

        if (continent == null) { //si no estás en tierra: barcos, la luna..
            continent = "none"; //podría perfectamente restringir acceso si no estás en un continente, pero prefiero dejarlo ilimitado. decisión de diseño
        }

        $("input[type=text]:last").val(continent);
        $("input[type=submit]").prop("disabled", false);
    }

    getCoordinates() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.locationSuccess.bind(this), this.locationError);
            $("p:last").text("Working correctly.");
            $("input[type=submit]").prop("disabled", false);
        } else {
            console.log("Location unavailable. Cannot proceed.");
        }
    }

    locationSuccess(data) {
        this.latitude = data.coords.latitude;
        this.longitude = data.coords.longitude;
        $("input[type=button]").prop("disabled", false);
    }

    locationError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                $("p:last").text("Location permission denied.");
                console.error();
                break;
            case error.POSITION_UNAVAILABLE:
                $("p:last").text("Location error: position unavailable.");
                break;
            case error.TIMEOUT:
                $("p:last").text("Location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                $("p:last").text("Location error: unknown.");
                break;
        }
    }

}

let qlm = new QuizListManager();
qlm.getCoordinates();