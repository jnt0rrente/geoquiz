"use strict"
class MineralPriceAPIObject {
    constructor() {
        this.url = "https://www.commodities-api.com/api/latest?access_key=z6y4cwoumau8ki7g6hxwjcli7ynceyj4gxlq3t57jhw7wrem321pxz7d9ypt&base=EUR&symbols=BRENTOIL%2CXAU";
    }

    update() {
        $.ajax({
            dataType: "json",
            url: this.url,
            method: "GET",
            success: this.success,
            error: this.error
        });
    }

    success(response) {
        console.log(JSON.stringify(response));
        console.log(this.url);

        var stringOutput = "<li>Gold: " + (1 / response.data.rates.XAU).toFixed(2) + "€ an ounce. </li>";
        stringOutput += "<li>Oil: " + (1 / response.data.rates.BRENTOIL).toFixed(2) + "€ a barrel. </li>";

        var ul = document.createElement("ul");
        ul.innerHTML = stringOutput;

        $("ul:last").remove();
        $("section:last").append(ul);
    }

    error() {
        console.log(JSON.stringify(response));
        console.log(this.url);

        var stringOutput = "<li>Gold: Error </li>";
        stringOutput += "<li>Oil: Error </li>";

        var ul = document.createElement("ul");
        ul.innerHTML = stringOutput;

        $("ul:last").remove();
        $("section:last").append(ul);
    }
}

let mineralApi = new MineralPriceAPIObject();